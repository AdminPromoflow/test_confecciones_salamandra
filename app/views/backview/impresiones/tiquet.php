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
            border-bottom: 1px dashed;
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
            border-bottom: 1px solid;
        }

        table {
            width: 100%;
            font-size: 12px;
        }

        .gracias {
            margin-top: 10px;
            padding-top: 10px;
            text-align: center;
            border-top: 1px solid;
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

        <div class="head">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td><b>Venta N°</b></td>
                    <td style="text-align:center;"></td>
                    <td style="text-align:right;">
                        <b><?= $venta->id_venta; ?></b>
                    </td>
                </tr>
                <tr>
                    <td>Fecha y hora</td>
                    <td style="text-align:center;"></td>
                    <td style="text-align:right; width: 60%; ">
                        <?= fecha_con_hora($venta->registro) ?>
                    </td>
                </tr>
                <tr>
                    <td>Vendedor</td>
                    <td style="text-align:center;"></td>
                    <td style="text-align:right;">
                        <?= ucfirst($venta->usuario) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="margin:0px;">Cliente cédula</p>
                        <p style="margin:0px;">Cliente nombre</p>
                        <p style="margin:0px;">Cliente email</p>
                    </td>
                    <td style="margin:0px; text-align:center; "></td>
                    <td style="text-align:right;">
                        <p style="margin:0px;"><?= $venta->cliente_cedula ?></p>
                        <p style="margin:0px;"><?= $venta->cliente_nombre . ' ' . $venta->cliente_apellidos ?></p>
                        <p style="margin:0px;"><?= $venta->cliente_email ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="transaction">
            <table class="bot">
                <th style="text-align: start;">Producto</th>
                <th style="text-align: start; width: 20px;"></th>
                <!-- <th>Valor Und</th> -->
                <th style="text-align: end;">Valor Total</th>
            </table>
            <table class="transaction-table bot" cellspacing="0" cellpadding="0">
                <tbody>
                    <?php
                    foreach ($detalle_venta as $detalle) { ?>
                        <tr style="border-bottom: 1px solid gray;">
                            <td style="width:165px; text-align: start; vertical-align: text-top;">
                                <?php if ($detalle->estado <= 1) : ?>
                                    <p style="margin-top: 0px; margin-bottom: 0px;margin-top: 0px;"><?= $detalle->codigo . ' - ' . $detalle->nombre_producto ?>
                                    <p style="margin-top: 5px; margin-bottom: 5px;"><em>Para el <?= fecha_sin_hora($detalle->fecha_entrega) ?></em></p>
                                <?php elseif ($detalle->estado == 2) : ?>
                                    <p style="margin-top: 0px;"><?= $detalle->codigo . ' - ' . $detalle->nombre_producto ?>
                                    <?php elseif ($detalle->estado == 3) : ?>
                                    <p style="margin-top: 0px;"><?= $detalle->codigo . ' - ' . $detalle->nombre_producto ?>
                                    <?php elseif ($detalle->estado == 4) : ?>
                                    <p style="margin-top: 0px; margin-bottom: 0px;margin-top: 0px;"><?= $detalle->codigo . ' - ' . $detalle->nombre_producto ?>
                                    <p style="margin-top: 5px; margin-bottom: 5px;"><em>Entregado el <?= fecha_sin_hora($detalle->fecha_actualizada) ?></em></p>
                                <?php elseif ($detalle->estado == 5) : ?>
                                    <p style="margin-top: 0px; margin-bottom: 0px;margin-top: 0px;"><strike><?= $detalle->nombre_producto ?></strike>
                                    <p style="margin-top: 5px; margin-bottom: 5px;"><em>Producto anulado</em></p>
                                <?php endif ?>
                                </p>
                            </td>
                            <td style="width: 25px; vertical-align: text-top;">
                                <p style="margin-top: 0px;">T. <?= $detalle->talla ?></p>
                            </td>
                            <td style="text-align:right; width:60px; vertical-align: text-top;">
                                <?php if ($detalle->estado == 6) : ?>
                                    <p style="margin-top: 0px;">* <?= formatearMoneda($detalle->precio) ?></p>
                                <?php else : ?>
                                    <p style="margin-top: 0px;"><?= formatearMoneda($detalle->precio) ?></p>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <table>
                <tr>
                    <td></td>
                    <td style="text-align: start;" colspan="10">
                        <p class="parrafo" style="font-size: 15px"><b>Gran Total</b></p>
                    </td>
                    <td style="text-align:right;">
                        <p class="parrafo" style="font-size: 15px"><b><?= formatearMoneda($venta->total) ?></b></p>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: start;" colspan="10">
                        <?php if ($venta->pagacon >= $venta->total) : ?>
                            <p class="parrafo">Efectivo</p>
                        <?php else : ?>
                            <?php if ($venta->descuento > 0) : ?>
                                <p class="parrafo" style="margin-bottom: 5px;">Descuento</p>
                            <?php endif ?>
                            <p class="parrafo">Abono</p>
                        <?php endif ?>
                    </td>
                    <td style="text-align: right;">
                        <?php if ($venta->pagacon > 0) : ?>
                            <p class="parrafo"><?= formatearMoneda($venta->pagacon) ?></p>
                        <?php else : ?>
                            <?php if ($venta->descuento > 0) : ?>
                                <p class="parrafo" style="margin-bottom: 5px;">- <?= formatearMoneda($venta->descuento) ?></p>
                            <?php endif ?>
                            <p class="parrafo"><?= formatearMoneda($venta->abono) ?></p>
                        <?php endif ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: start;" colspan="10">
                        <?php if ($venta->pagacon >= $venta->total) : ?>
                            <p class="parrafo">Cambio</p>
                        <?php else : ?>
                            <p class="parrafo">Saldo pendiente</p>
                        <?php endif ?>
                    </td>
                    <td style="text-align:right;">
                        <?php if ($venta->pagacon >= $venta->total) : ?>
                            <p class="parrafo"><?= ($venta->pagacon < $venta->total) ? formatearMoneda($venta->cambio * -1) : formatearMoneda($venta->cambio) ?></p>
                        <?php elseif ($venta->abono > 0 && $venta->descuento > 0) : ?>
                            <p class="parrafo"><?= formatearMoneda(($venta->total - $venta->descuento) - $venta->abono) ?></p>
                        <?php elseif ($venta->abono > 0) : ?>
                            <p class="parrafo"><?= formatearMoneda($venta->total - $venta->abono) ?></p>
                        <?php endif ?>
                    </td>
                </tr>
            </table>

            <?php if (!empty($venta->nota)) { ?>
                <div style="border: 1px solid gray; margin-top: 15px; padding: 5px;">
                    <p style="margin-top: 0px;"><strong>Nota:</strong> <?= $venta->nota ?></p>
                </div>
            <?php } ?>

            <div class="gracias" style="padding-top: 0px;">
                <p style="margin-bottom: 0px;">Las órdenes de confección tienen <strong>validez de 30 días</strong> después de la fecha de entrega.</p>
                <p style="margin: 0px;"><strong>No se hace devolución de DINERO</strong></p>
                <p>www.confeccionessalamandra.com</p>
                <p>ventas@confeccionessalamandra.com</p>
            </div>
        </div>
    </div>
</body>

</html>