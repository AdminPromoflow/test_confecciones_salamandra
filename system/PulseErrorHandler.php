<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * La clase ErrorHandler
 */
class PulseErrorHandler extends Exception
{
    // Método estático para manejar una excepción pasada como argumento.
    public static function handleException(Exception $exception)
    {
        // Obtener el rastro (trace) de la excepción para determinar su origen.
        $trace = $exception->getTrace();
        // Acceder al primer elemento del rastro, que generalmente es la información de la llamada.
        $first_trace = $trace[0]['class'];

        // Comprobar si la excepción proviene de la clase 'Router'.
        if ($first_trace == 'Router') {
            // Si es así, manejar la excepción específica del enrutamiento.
            self::handleRoutingException($exception);
        } else {
            // De lo contrario, manejar la excepción genérica.
            self::handleGenericException($exception);
        }
    }

    // Método para manejar excepciones específicas de enrutamiento.
    public static function handleRoutingException($exception)
    {
        // Aquí se puede personalizar el manejo de excepciones relacionadas con el enrutamiento.
        // Por ahora, simplemente se imprime el mensaje de la excepción.
        echo $exception->getMessage();
    }

    //Método para manejar excepciones genéricas.
    public static function handleGenericException($exception)
    {
        // Aquí se puede personalizar el manejo de excepciones genéricas.
        // Por ahora, simplemente se imprime el mensaje de la excepción.
        echo $exception->getMessage();
    }
}
