<?php

function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


function validarRFC($rfc) {
    // Expresión regular para validar RFC de personas físicas
    $regexPersonaFisica = '/^([A-ZÑ&]{4})(\d{6})([A-Z\d]{3})$/';
    
    // Expresión regular para validar RFC de personas morales
    $regexPersonaMoral = '/^([A-ZÑ&]{3})(\d{6})([A-Z\d]{3})$/';
    
    // Si coincide con el formato de persona física
    if (preg_match($regexPersonaFisica, $rfc)) {
        // Se verifica la homoclave
        $homoclave = substr($rfc, -3);
        $claveSinHomoclave = substr($rfc, 0, -3);
        $sum = 0;
        for ($i = 0; $i < strlen($claveSinHomoclave); $i++) {
            $char = $claveSinHomoclave[$i];
            if ($i % 2 === 0) {
                $sum += (int)$char;
            } else {
                $sum += ord($char) - ord('A') + 10;
            }
        }
        $remainder = $sum % 10;
        $expectedHomoclave = ($remainder === 0) ? 0 : (10 - $remainder);
        return $expectedHomoclave == $homoclave;
    }
    
    // Si coincide con el formato de persona moral
    if (preg_match($regexPersonaMoral, $rfc)) {
        // Se verifica la homoclave
        $homoclave = substr($rfc, -3);
        $claveSinHomoclave = substr($rfc, 0, -3);
        $sum = 0;
        for ($i = 0; $i < strlen($claveSinHomoclave); $i++) {
            $char = $claveSinHomoclave[$i];
            if ($i % 2 === 0) {
                $sum += (int)$char;
            } else {
                $sum += ord($char) - ord('A') + 10;
            }
        }
        $remainder = $sum % 11;
        $expectedHomoclave = ($remainder === 0) ? '0' : (11 - $remainder);
        if ($expectedHomoclave == 10) {
            $expectedHomoclave = 'A';
        }
        return $expectedHomoclave == $homoclave;
    }
    
    // Si no coincide con ninguno de los formatos
    return false;
}
