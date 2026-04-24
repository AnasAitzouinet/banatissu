<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bon de livraison _{{$sale['Ref']}}</title>
    <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />
</head>

<body>
<header class="clearfix" style="marign-top:100px">
    <div id="logo">
        <img src="{{asset('/images/'.$setting['logo'])}}">
    </div>
    <div id="company">
        <div><strong> Date : </strong>{{$sale['date']}}</div>
        <div><strong> Référence : </strong> {{$sale['Ref']}}</div>
        <div><strong> Statut : </strong> {{$sale['statut']}}</div>
        <div><strong> Statut de paiement : </strong> {{$sale['payment_status']}}</div>
    </div>
    <div id="Title-heading">
        Bon de livraison : {{$sale['Ref']}}
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
                <th>QUANTITÉ / MÈTRES</th>
                <th>PIÈCES</th>
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
                    <td>{{$detail['pieces_count'] ?? 0}}</td>
                    <td>{{$detail['total']}} </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div id="total">
        <table>
            <tr>
                <td>Référence</td>
                <td>{{$sale['Ref']}}</td>
            </tr>
            <tr>
                <td>Total metrage</td>
                <td>{{$sale['total_metrage']}}</td>
            </tr>
            <tr>
                <td>Total pieces</td>
                <td>{{$sale['total_pieces']}}</td>
            </tr>
            <tr>
                <td>Prix final</td>
                <td>{{$symbol}} {{$sale['GrandTotal']}} </td>
            </tr>
            <tr>
                <td>Reste à payer</td>
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
