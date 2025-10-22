<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture _{{$sale['Ref']}}</title>
    <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />
</head>

<body>
<header class="clearfix" style="marign-top:100px">
    <div id="logo">
        <img src="{{asset('/images/'.$setting['logo'])}}">
    </div>
    <div id="company">
        <div><strong> Date : </strong>{{$sale['date']}}</div>
        <div><strong> Numéro : </strong> {{$sale['Ref']}}</div>
        <div><strong> Statut : </strong> {{$sale['statut']}}</div>
        <div><strong> Statut de paiement : </strong> {{$sale['payment_status']}}</div>
        @if($sale['reglement'])<div><strong>Règlement :</strong>  {{$sale['reglement']}}</div>@endif
    </div>
    <div id="Title-heading">
        Vente : {{$sale['Ref']}}
    </div>
</header>
<main>
    <div id="details" class="clearfix">
        <div id="client">
            <table class="table-sm">
                <thead>
                <tr>
                    <th class="desc">Infos client</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div><strong>Nom complet :</strong> {{$sale['client_name']}}</div>
                        <div><strong>Téléphone :</strong> {{$sale['client_phone']}}</div>
                        <div><strong>Ville :</strong>  {{$sale['client_city'] ?? ''}}</div>
                        {{-- <div><strong>Adresse :</strong>   {{$sale['client_adr']}}</div> --}}
                        @if($sale['client_tax'])<div><strong>Numéro fiscal :</strong>  {{$sale['client_tax']}}</div>@endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="invoice">
            <table class="table-sm">
                <thead>
                <tr>
                    <th class="desc">Infos société</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div id="comp">{{$setting['CompanyName']}}</div>
                        <div><strong>Téléphone :</strong>  {{$setting['CompanyPhone']}}</div>
                        {{--<div><strong>Email :</strong>  {{$setting['email']}}</div>--}}
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
                <td>{{$sale['TaxNet']}} </td>
            </tr>
            <tr>
                <td>Remise</td>
                <td>{{$sale['discount']}} </td>
            </tr>
            <tr>
                <td>Expédition</td>
                <td>{{$sale['shipping']}} </td>
            </tr>
            <tr>
                <td>Total</td>
                <td>{{$symbol}} {{$sale['GrandTotal']}} </td>
            </tr>

            <tr>
                <td>Montant payé</td>
                <td>{{$symbol}} {{$sale['paid_amount']}} </td>
            </tr>

            <tr>
                <td>Montant dû</td>
                <td>{{$symbol}} {{$sale['due']}} </td>
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
