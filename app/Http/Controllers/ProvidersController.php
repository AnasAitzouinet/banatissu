<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\Setting;
use App\utils\helpers;
use Carbon\Carbon;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Purchase;
use App\Models\PaymentPurchase;
use App\Models\PurchaseReturn;
use App\Models\PaymentPurchaseReturns;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Http\Request;

class ProvidersController extends BaseController
{

    //----------- Get ALL Suppliers-------\\

    public function index(request $request)
    {
        $this->authorizeForUser($request->user('api'), 'view', Provider::class);

        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $order = $request->SortField;
        $dir = $request->SortType;
        $helpers = new helpers();
        // Filter fields With Params to retrieve
        $columns = array(0 => 'name', 1 => 'code', 2 => 'phone', 3 => 'email');
        $param = array(0 => 'like', 1 => 'like', 2 => 'like', 3 => 'like');
        $data = array();

        $providers = Provider::where('deleted_at', '=', null);

        //Multiple Filter
        $Filtred = $helpers->filter($providers, $columns, $param, $request)
        // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('code', 'LIKE', "%{$request->search}%")
                        ->orWhere('phone', 'LIKE', "%{$request->search}%")
                        ->orWhere('email', 'LIKE', "%{$request->search}%");
                });
            });
        $totalRows = $Filtred->count();
        if($perPage == "-1"){
            $perPage = $totalRows;
        }
        $providers = $Filtred->offset($offSet)
            ->limit($perPage)
            ->orderBy($order, $dir)
            ->get();

        foreach ($providers as $provider) {

            $item['total_amount'] = DB::table('purchases')
                ->where('deleted_at', '=', null)
                ->where('statut', 'received')
                ->where('provider_id', $provider->id)
                ->sum('GrandTotal');

            $item['total_paid'] = DB::table('purchases')
                ->where('deleted_at', '=', null)
                ->where('statut', 'received')
                ->where('provider_id', $provider->id)
                ->sum('paid_amount');

            $item['due'] = $item['total_amount'] - $item['total_paid'];

            $item['total_amount_return'] = DB::table('purchase_returns')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->sum('GrandTotal');

            $item['total_paid_return'] = DB::table('purchase_returns')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->sum('paid_amount');

            $item['return_Due'] = $item['total_amount_return'] - $item['total_paid_return'];

            $item['id'] = $provider->id;
            $item['name'] = $provider->name;
            $item['phone'] = $provider->phone;
            $item['tax_number'] = $provider->tax_number;
            $item['code'] = $provider->code;
            $item['email'] = $provider->email;
            $item['country'] = $provider->country;
            $item['city'] = $provider->city;
            $item['adresse'] = $provider->adresse;
            $data[] = $item;
        }

        $company_info = Setting::where('deleted_at', '=', null)->first();
        $accounts = Account::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','account_name']);

        return response()->json([
            'providers' => $data,
            'company_info' => $company_info,
            'totalRows' => $totalRows,
            'accounts' => $accounts,
        ]);
    }

    //----------- Store new Supplier -------\\

    public function store(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'create', Provider::class);

        request()->validate([
            'name' => 'required',
        ]);
        Provider::create([
            'name' => $request['name'],
            'code' => $this->getNumberOrder(),
            'adresse' => $request['adresse'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'country' => $request['country'],
            'city' => $request['city'],
            'tax_number' => $request['tax_number'],
        ]);
        return response()->json(['success' => true]);

    }

    //------------ function show -----------\\

    public function show($id){
        //
        
        }

    //----------- Update Supplier-------\\

    public function update(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', Provider::class);

        request()->validate([
            'name' => 'required',
        ]);

        Provider::whereId($id)->update([
            'name' => $request['name'],
            'adresse' => $request['adresse'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'country' => $request['country'],
            'city' => $request['city'],
            'tax_number' => $request['tax_number'],
        ]);
        return response()->json(['success' => true]);

    }

    //----------- Remdeleteove Provider-------\\

    public function destroy(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'delete', Provider::class);

        Provider::whereId($id)->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json(['success' => true]);

    }

    //-------------- Delete by selection  ---------------\\

    public function delete_by_selection(Request $request)
    {

        $this->authorizeForUser($request->user('api'), 'delete', Provider::class);

        $selectedIds = $request->selectedIds;
        foreach ($selectedIds as $Provider_id) {
            Provider::whereId($Provider_id)->update([
                'deleted_at' => Carbon::now(),
            ]);
        }
        return response()->json(['success' => true]);
    }


    //----------- get Number Order Of Suppliers-------\\

    public function getNumberOrder()
    {

        $last = DB::table('providers')->latest('id')->first();

        if ($last) {
            $code = $last->code + 1;
        } else {
            $code = 1;
        }
        return $code;
    }

    // import providers
    public function import_providers(Request $request)
    {
        $file_upload = $request->file('providers');
        $ext = pathinfo($file_upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if ($ext != 'csv') {
            return response()->json([
                'msg' => 'must be in csv format',
                'status' => false,
            ]);
        } else {
            $data = array();
            $rowcount = 0;
            if (($handle = fopen($file_upload, "r")) !== false) {
                $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
                $header = fgetcsv($handle, $max_line_length, ';'); // Use semicolon as the delimiter
                $header_colcount = count($header);
                while (($row = fgetcsv($handle, $max_line_length, ';')) !== false) { // Use semicolon as the delimiter
                    $row_colcount = count($row);
                    if ($row_colcount == $header_colcount) {
                        $entry = array_combine($header, $row);
                        $data[] = $entry;
                    } else {
                        return null;
                    }
                    $rowcount++;
                }
                fclose($handle);
            } else {
                return null;
            }

            $rules = [];

            //-- Create New Provider
            foreach ($data as $key => $value) {
                
                $validator = Validator::make($value, $rules);
                if (!$validator->fails()) {
                    $value = $this->cleanArrayKeys($value);
                    
                    Provider::create([
                        'name' => $value['name'],
                        'code' => $this->getNumberOrder(),
                        'phone' => $value['phone'] == '' ? null : $value['phone'],
                        'email' => $value['email'] == '' ? null : $value['email'],
                        'pays' => $value['pays'] == '' ? null : $value['pays'],
                        'ville' => $value['ville'] == '' ? null : $value['ville'],
                        'adresse' => $value['adresse'] == '' ? null : $value['adresse'],
                        'ICE' => $value['ICE'] == '' ? null : $value['ICE'],
                    ]);
                }
                
            }

            return response()->json([
                'status' => true,
            ], 200);
        }

    }


    //------------- pay_supplier_due -------------\\

    public function pay_supplier_due(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'pay_supplier_due', Provider::class);
       
        if($request['amount'] > 0){
           $provider_purchases_due = Purchase::where('deleted_at', '=', null)
           ->where('statut', 'received')
           ->where([
               ['payment_statut', '!=', 'paid'],
               ['provider_id', $request->provider_id]
           ])->get();

           $paid_amount_total = $request->amount;

           foreach($provider_purchases_due as $key => $provider_purchase){
               if($paid_amount_total == 0)
               break;
               $due = $provider_purchase->GrandTotal  - $provider_purchase->paid_amount;

               if($paid_amount_total >= $due){
                   $amount = $due;
                   $payment_status = 'paid';
               }else{
                   $amount = $paid_amount_total;
                   $payment_status = 'partial';
               }

               $payment_purchase = new PaymentPurchase();
               $payment_purchase->purchase_id = $provider_purchase->id;
               $payment_purchase->account_id =  $request['account_id']?$request['account_id']:NULL;
               $payment_purchase->Ref = app('App\Http\Controllers\PaymentPurchasesController')->getNumberOrder();
               $payment_purchase->date = Carbon::now();
               $payment_purchase->Reglement = $request['Reglement'];
               $payment_purchase->montant = $amount;
               $payment_purchase->change = 0;
               $payment_purchase->notes = $request['notes'];
               $payment_purchase->user_id = Auth::user()->id;
               $payment_purchase->save();

               $account = Account::where('id', $request['account_id'])->exists();

               if ($account) {
                   // Account exists, perform the update
                   $account = Account::find($request['account_id']);
                   $account->update([
                       'balance' => $account->balance - $amount,
                   ]);
               }

               $provider_purchase->paid_amount += $amount;
               $provider_purchase->payment_statut = $payment_status;
               $provider_purchase->save();

               $paid_amount_total -= $amount;
           }
       }
       
        return response()->json(['success' => true]);

    }

     //------------- pay_purchase_return_due -------------\\

    public function pay_purchase_return_due(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'pay_purchase_return_due', Provider::class);
        
        if($request['amount'] > 0){
            $supplier_purchase_return_due = PurchaseReturn::where('deleted_at', '=', null)
            ->where([
                ['payment_statut', '!=', 'paid'],
                ['provider_id', $request->provider_id]
            ])->get();

            $paid_amount_total = $request->amount;

            foreach($supplier_purchase_return_due as $key => $supplier_purchase_return){
                if($paid_amount_total == 0)
                break;
                $due = $supplier_purchase_return->GrandTotal  - $supplier_purchase_return->paid_amount;

                if($paid_amount_total >= $due){
                    $amount = $due;
                    $payment_status = 'paid';
                }else{
                    $amount = $paid_amount_total;
                    $payment_status = 'partial';
                }

                $payment_purchase_return = new PaymentPurchaseReturns();
                $payment_purchase_return->purchase_return_id = $supplier_purchase_return->id;
                $payment_purchase_return->account_id =  $request['account_id']?$request['account_id']:NULL;
                $payment_purchase_return->Ref = app('App\Http\Controllers\PaymentPurchaseReturnsController')->getNumberOrder();
                $payment_purchase_return->date = Carbon::now();
                $payment_purchase_return->Reglement = $request['Reglement'];
                $payment_purchase_return->montant = $amount;
                $payment_purchase_return->change = 0;
                $payment_purchase_return->notes = $request['notes'];
                $payment_purchase_return->user_id = Auth::user()->id;
                $payment_purchase_return->save();

                $account = Account::where('id', $request['account_id'])->exists();

                if ($account) {
                    // Account exists, perform the update
                    $account = Account::find($request['account_id']);
                    $account->update([
                        'balance' => $account->balance + $amount,
                    ]);
                }

                $supplier_purchase_return->paid_amount += $amount;
                $supplier_purchase_return->payment_statut = $payment_status;
                $supplier_purchase_return->save();

                $paid_amount_total -= $amount;
            }
        }
        
        return response()->json(['success' => true]);

    }
    
    public function cleanArrayKeys(array $arr): array {
    $clean = [];
    foreach ($arr as $key => $value) {
        // Remove invisible/unprintable characters
        $newKey = preg_replace('/[[:^print:]]/', '', $key);
        // Trim normal spaces/tabs/newlines
        $newKey = trim($newKey);

        // If value is also an array, clean recursively
        if (is_array($value)) {
            $value = 
            $this->cleanArrayKeys($value);
        }

        $clean[$newKey] = $value;
    }
    return $clean;
}

}
