<?php

function validaSesion(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['id'])){
        return true;
    }
    return false;
}
function autenticacion(){
    $input_json = json_decode(file_get_contents('php://input'));
    
    $usuario = $input_json->usuario ?? ($_POST['usuario'] ?? '');
    $password = $input_json->password ?? ($_POST['password'] ?? '');

    if($usuario === "userupla" && $password === "upla123"){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['usuario'] = $usuario;
        $_SESSION['id'] = rand(1, 5000);
        return [
            "mensaje" => "SESIÓN INICIADA CORRECTAMENTE",
            "usuario" => $_SESSION['usuario'],
            "id" => $_SESSION['id']
        ];
    } else {
        http_response_code(401);
        return ["error" => "NO AUTORIZADO"];
    }
}

function listarParticipantes(){
    if(!validaSesion()){
        http_response_code(401);
        return ["error" => "NO PERMITIDO. DEBE INICIAR SESIÓN"];
    }
    if(!file_exists('participantes.json')){
        return [];
    }
    $contenido = file_get_contents('participantes.json');
    return json_decode($contenido, true);
}

function listarPremios(){
    if(!validaSesion()){
        http_response_code(401);
        return ["error" => "NO PERMITIDO. DEBE INICIAR SESIÓN"];
    }
    if(!file_exists('premios.json')){
        return [];
    }
    $contenido = file_get_contents('premios.json');
    return json_decode($contenido, true);
}
function registrarParticipante(){
    if(!validaSesion()){
        http_response_code(401);
        return ["error" => "NO PERMITIDO. DEBE INICIAR SESIÓN"];
    }
    
    $input_json = json_decode(file_get_contents('php://input'), true);
    $nombre = $input_json['nombre'] ?? ($_POST['nombre'] ?? null);
    $apellido = $input_json['apellido'] ?? ($_POST['apellido'] ?? null);
    $numero_rifa = $input_json['numero_rifa'] ?? ($_POST['numero_rifa'] ?? null);

    if(!$nombre || !$apellido || $numero_rifa === null){
        http_response_code(400);
        return ["error" => "FALTAN DATOS (nombre, apellido, numero_rifa)"];
    }

    $participantes = file_exists('participantes.json') 
        ? json_decode(file_get_contents('participantes.json'), true) 
        : [];

    foreach($participantes as $p){
        if(isset($p['numero_rifa']) && (int)$p['numero_rifa'] === (int)$numero_rifa){
            http_response_code(400);
            return ["error" => "EL NÚMERO DE RIFA $numero_rifa YA ESTÁ UTILIZADO"];
        }
    }

    $nuevo_id = 1;
    foreach($participantes as $p){
        if(isset($p['id']) && $p['id'] >= $nuevo_id){
            $nuevo_id = $p['id'] + 1;
        }
    }

    $nuevo = [
        "id" => $nuevo_id,
        "nombre" => $nombre,
        "apellido" => $apellido,
        "numero_rifa" => (int)$numero_rifa
    ];

    $participantes[] = $nuevo;
    file_put_contents('participantes.json', json_encode($participantes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return [
        "mensaje" => "PARTICIPANTE REGISTRADO",
        "participante" => $nuevo
    ];
}

function actualizarParticipante(){
    if(!validaSesion()){
        http_response_code(401);
        return ["error" => "NO PERMITIDO. DEBE INICIAR SESIÓN"];
    }

    $input_json = json_decode(file_get_contents('php://input'), true);
    $id = $input_json['id'] ?? ($_POST['id'] ?? null);

    if(!$id){
        http_response_code(400);
        return ["error" => "DEBE INDICAR EL ID DEL PARTICIPANTE"];
    }

    $participantes = file_exists('participantes.json') 
        ? json_decode(file_get_contents('participantes.json'), true) 
        : [];

    $encontrado = false;
    $actualizado = null;

    foreach($participantes as &$p){
        if($p['id'] == $id){
            // Validar que el nuevo número no lo tenga otro participante
            if(isset($input_json['numero_rifa']) && (int)$input_json['numero_rifa'] !== (int)$p['numero_rifa']){
                foreach($participantes as $otro_p){
                    if($otro_p['id'] != $id && isset($otro_p['numero_rifa']) && (int)$otro_p['numero_rifa'] === (int)$input_json['numero_rifa']){
                        http_response_code(400);
                        return ["error" => "EL NUEVO NÚMERO DE RIFA YA ESTÁ UTILIZADO"];
                    }
                }
            }

            $p['nombre'] = $input_json['nombre'] ?? ($p['nombre']);
            $p['apellido'] = $input_json['apellido'] ?? ($p['apellido']);
            $p['numero_rifa'] = isset($input_json['numero_rifa']) ? (int)$input_json['numero_rifa'] : $p['numero_rifa'];
            
            $encontrado = true;
            $actualizado = $p;
            break;
        }
    }

    if(!$encontrado){
        http_response_code(404);
        return ["error" => "PARTICIPANTE NO ENCONTRADO"];
    }

    file_put_contents('participantes.json', json_encode($participantes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return [
        "mensaje" => "PARTICIPANTE ACTUALIZADO",
        "participante" => $actualizado
    ];
}

function eliminarParticipante(){
    if(!validaSesion()){
        http_response_code(401);
        return ["error" => "NO PERMITIDO. DEBE INICIAR SESIÓN"];
    }

    if(!isset($_GET['id'])){
        http_response_code(400);
        return ["error" => "DEBE ENVIAR EL PARAMETRO id POR GET"];
    }

    $id = $_GET['id'];
    $participantes = file_exists('participantes.json') 
        ? json_decode(file_get_contents('participantes.json'), true) 
        : [];

    $nuevos_participantes = [];
    $eliminado = false;

    foreach($participantes as $p){
        if($p['id'] == $id){
            $eliminado = true;
        } else {
            $nuevos_participantes[] = $p;
        }
    }

    if(!$eliminado){
        http_response_code(404);
        return ["error" => "PARTICIPANTE NO ENCONTRADO"];
    }

    file_put_contents('participantes.json', json_encode($nuevos_participantes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return ["mensaje" => "PARTICIPANTE ELIMINADO CORRECTAMENTE"];
}

function cierre_sesion(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    return [
        "respuesta" => "SESIÓN DESTRUIDA"
    ];
}

function accionNoEncontrada(){
    http_response_code(404);
    return ["error" => "SERVICIO NO ENCONTRADO"];
}
?>