<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <title>Confecciones Salamandra</title>
    <style type="text/css">
        html {
            font-family: "Arial";
        }

        .content {
            width: 74mm;
            font-size: 12px;
            padding: 0px;
        }

        .title {
            text-align: center;
            font-size: 13px;
            padding-bottom: 5px;
        }

        .head {
            margin-top: 5px;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid;
        }

        .bot {
            margin-top: 5px;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            font-size: 12px;
        }

        .gracias {
            margin-top: 10px;
            padding-top: 10px;
            text-align: center;
        }

        .parrafo {
            margin: 0px;
            padding: 0px;
        }

        @media print {
            @page {
                width: 74mm;
                margin: 0mm;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="content">
        <div class="title">
            <h2 style="margin-bottom: 2px;">Confecciones Salamandra</h2>
            <p style="margin: 2px;">Nit. 52490405-1</p>
            <p style="margin: 2px;">Cel. 312 485-8575 / 311 899-3908</p>
            <p style="margin: 2px;">Fusagasugá / Cundinamarca</p>
        </div>

        <hr>

        <div>
            <h4 style="text-align: center; margin-bottom: 5px;">Abonos Realizados a la factura de venta</h4>
            <h2 style="text-align: center; margin-bottom: 5px;">No. <?= $abonos[0]->id_venta ?></h2>
        </div>

        <hr>

        <table class="table transaction-table bot" cellspacing="0" cellpadding="0">
            <tr>
                <td><b>Total de la venta</b></td>
                <td style="text-align:center;"></td>
                <td style="text-align:right;">
                    <p><?= formatearMoneda($abonos[0]->total) ?></p>
                </td>
            </tr>
        </table>

        <hr>

        <?php if (!empty($abonos)) : ?>
            <div class="abonos">
                <table class="table transaction-table bot" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th>Fecha del abono</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($abonos as $abono) : ?>
                            <tr>
                                <td>
                                    <p><?= fecha_con_hora($abono->registro) ?></p>
                                </td>
                                <td style="text-align: right;">
                                    <p><?= formatearMoneda($abono->valor) ?></p>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <hr>

        <table class="table transaction-table bot" cellspacing="0" cellpadding="0">
            <tr>
                <td><b>Saldo pendiente</b></td>
                <td style="text-align:center;"></td>
                <td style="text-align:right; width: 60%; ">
                    <p><?= formatearMoneda($abonos[0]->saldo) ?></p>
                </td>
            </tr>
        </table>

        <hr>

        <div class="gracias" style="padding-top: 0px;">
            <p>www.confeccionessalamandra.com</p>
            <p>ventas@confeccionessalamandra.com</p>
        </div>
    </div>
</body>

</html>