<!DOCTYPE html>
<html lang="fr">
   <head>
      <meta charset="utf-8">
      <title>Devis _{{$quote['Ref']}}</title>
      <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />
   </head>

   <body>
      <header class="clearfix">
         <div id="logo">
         <img src="{{asset('/images/'.$setting['logo'])}}">
         </div>
         <div id="company">
            <div><strong> Date : </strong>{{$quote['date']}}</div>
            <div><strong> Numéro : </strong> {{$quote['Ref']}}</div>
            <div><strong> Statut : </strong> {{$quote['statut']}}</div>
         </div>
         <div id="Title-heading">
            Devis : {{$quote['Ref']}}
         </div>
         </div>
      </header>
      <main>
         <div id="details" class="clearfix">
            <div id="client">
               <table class="table-sm">
                  <thead>
                     <tr>
                        <th class="desc">Informations Client</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <div><strong>Nom Complet :</strong> {{$quote['client_name']}}</div>
                           <div><strong>Téléphone :</strong> {{$quote['client_phone']}}</div>
{{--                           <div><strong>Email :</strong>  {{$quote['client_email']}}</div>--}}
{{--                           <div><strong>Adresse :</strong>   {{$quote['client_adr']}}</div> --}}
                            <div><strong>Ville :</strong> {{$quote['client_city'] ?? null}}</div>
                           @if($quote['client_tax'])<div><strong>Numéro Fiscal :</strong>  {{$quote['client_tax']}}</div>@endif
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div id="invoice">
               <table class="table-sm">
                  <thead>
                     <tr>
                        <th class="desc">Informations Société</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <div id="comp">{{$setting['CompanyName']}}</div>
                           <div><strong>Téléphone :</strong>  {{$setting['CompanyPhone']}}</div>
{{--                           <div><strong>Email :</strong>  {{$setting['email']}}</div>--}}
                           <div><strong>Adresse :</strong>  {{$setting['CompanyAdress']}}</div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
         <div id="details_inv">
            <table class="table-sm">
               <thead>
                  <tr>
                     <th>PRODUIT</th>
                     <th>PRIX UNITAIRE</th>
                     <th>QUANTITÉ</th>
                     <th>REMISSE</th>
                     <th>TAXE</th>
                     <th>TOTAL</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($details as $detail)
                  <tr>
                     <td>
                        <span>{{$detail['code']}} ({{$detail['name']}})</span>
                           @if($detail['is_imei'] && $detail['imei_number'] !==null)
                              <p>IMEI/SN : {{$detail['imei_number']}}</p>
                           @endif
                     </td>
                     <td>{{$detail['price']}} </td>
                     <td>{{$detail['quantity']}}/{{$detail['unitSale']}}</td>
                     <td>{{$detail['DiscountNet']}} </td>
                     <td>{{$detail['taxe']}} </td>
                     <td>{{$detail['total']}} </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
         <div id="total">
            <table>
               <tr>
                  <td>Taxe sur la commande</td>
                  <td>{{$quote['TaxNet']}} </td>
               </tr>
               <tr>
                  <td>Remise</td>
                  <td>{{$quote['discount']}} </td>
               </tr>
               <tr>
                  <td>Expédition</td>
                  <td>{{$quote['shipping']}} </td>
               </tr>
               <tr>
                  <td>Total</td>
                  <td>{{$symbol}} {{$quote['GrandTotal']}} </td>
               </tr>
            </table>
         </div>
         <div id="signature">
            @if($setting['is_invoice_footer'] && $setting['invoice_footer'] !==null)
               <p>{{$setting['invoice_footer']}}</p>
            @endif
         </div>
      </main>
   </body>
</html>
