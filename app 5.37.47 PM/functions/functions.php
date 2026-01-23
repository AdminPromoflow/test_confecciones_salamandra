<?php

function userSession()
{
    require_once SYSTEM_PATH . 'PulseSessions.php';
    return new PulseSessions();
}

function showTest($data, $linea = '')
{
    if ($data == true) {
        echo '<pre>';
        echo 'Test ralizado en la línea ' . $linea;
        echo '<br/><br/>';
        print_r($data);
        echo '</pre>';
    } else {
        echo 'Error. Test no pasado en la línea ' . $linea;
    }

    exit();
}

function url_path($target_page = '')
{
    return $target_page == '' ? URL_PATH : URL_PATH . $target_page;
}

function adminAccess()
{
    return isLoggedIn(1) && permission(1);
}

function sellerAccess()
{
    return isLoggedIn(1) && permission(2);
}

function OperatorAccess()
{
    return isLoggedIn(1) && permission(3);
}

// Comprobar si un usuario ha iniciado sesión
function isLoggedIn($data)
{
    $isLoggedIn = userSession()->getUserData('userSession', 'isLoggedIn');
    return $isLoggedIn == $data ? TRUE : FALSE;
}

function permission($data)
{
    $permiso = userSession()->getUserData('userSession', 'usuarioPermiso');
    return $permiso == $data ? TRUE : FALSE;
}

function formatearMoneda($monto)
{
    // Formatear el monto como moneda colombiana
    $montoFormateado = number_format($monto, 0, ',', '.');
    // Agregar el símbolo del peso colombiano
    $montoFormateado = "$ $montoFormateado";
    return $montoFormateado;
}

function formatearFecha($fecha)
{
    $fechaObj = new DateTime($fecha);
    $fechaFormateada = $fechaObj->format('d-m-Y');
    return $fechaFormateada;
}

function fecha_con_hora($string)
{
    $dia_sem = date('w', strtotime($string));

    if ($dia_sem == 0) {
        $semana = "Domingo";
    } elseif ($dia_sem == 1) {
        $semana = "Lunes";
    } elseif ($dia_sem == 2) {
        $semana = "Martes";
    } elseif ($dia_sem == 3) {
        $semana = "Miercoles";
    } elseif ($dia_sem == 4) {
        $semana = "Jueves";
    } elseif ($dia_sem == 5) {
        $semana = "Viernes";
    } else {
        $semana = "Sábado";
    }

    $dia = date('d', strtotime($string));

    $mes = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"][date('n', strtotime($string)) - 1];

    $mes_num = date('M', strtotime($string));

    $ano = date('Y', strtotime($string));

    $hora = date('g:i a', strtotime($string));

    return $semana . ' ' . $dia . '/' . $mes . '/' . $ano . ' ' . $hora;
}

function fecha_sin_hora($string)
{
    // Verifica si la cadena de entrada no está vacía
    if (empty($string)) {
        return 'Fecha no válida';
    }
    
    $dia_sem = date('w', strtotime($string));

    if ($dia_sem == 0) {
        $semana = "Domingo";
    } elseif ($dia_sem == 1) {
        $semana = "Lunes";
    } elseif ($dia_sem == 2) {
        $semana = "Martes";
    } elseif ($dia_sem == 3) {
        $semana = "Miercoles";
    } elseif ($dia_sem == 4) {
        $semana = "Jueves";
    } elseif ($dia_sem == 5) {
        $semana = "Viernes";
    } else {
        $semana = "Sábado";
    }

    $dia = date('d', strtotime($string));

    $mes = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"][date('n', strtotime($string)) - 1];

    $mes_num = date('M', strtotime($string));

    $ano = date('Y', strtotime($string));

    return $semana . ' ' . $dia . '/' . $mes . '/' . $ano;
}
