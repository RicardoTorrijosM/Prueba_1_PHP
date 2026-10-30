<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
require_once 'funciones.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? '';

switch($accion){
    case 'login':
        if($metodo == 'POST'){ echo json_encode(autenticacion()); } else { echo json_encode(accionNoEncontrada()); }
        break;
    case 'participantes':
        if($metodo == 'GET'){ echo json_encode(listarParticipantes()); } else { echo json_encode(accionNoEncontrada()); }
        break;
    case 'premios':
        if($metodo == 'GET'){ echo json_encode(listarPremios()); } else { echo json_encode(accionNoEncontrada()); }
        break;
    case 'registrar':
        if($metodo == 'POST'){ echo json_encode(registrarParticipante()); } else { echo json_encode(accionNoEncontrada()); }
        break;
    case 'actualizar':
        if($metodo == 'POST'){ echo json_encode(actualizarParticipante()); } else { echo json_encode(accionNoEncontrada()); }
        break;
    case 'eliminar':
        if($metodo == 'GET'){ echo json_encode(eliminarParticipante()); } else { echo json_encode(accionNoEncontrada()); }
        break;
    case 'logout':
        if($metodo == 'GET'){ echo json_encode(cierre_sesion()); } else { echo json_encode(accionNoEncontrada()); }
        break;
    default:
        echo json_encode(accionNoEncontrada());
        break;
}
?>