<?php

namespace App\Manager;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidPayloadException;

class PayloadManager
{
    // Constantes de gestion des types

    /* const PAYLOAD_DATA_TYPE_BOOLEAN = 'bool';
    const PAYLOAD_DATA_TYPE_DATE = 'Date';
    const PAYLOAD_DATA_TYPE_DATETIME = 'DateTime';
    const PAYLOAD_DATA_TYPE_FLOAT  = 'float';
    const PAYLOAD_DATA_TYPE_INT = 'int';
    const PAYLOAD_DATA_TYPE_JSON = 'json';
    const PAYLOAD_DATA_TYPE_RAW = 'raw';
    const PAYLOAD_DATA_TYPE_ARRAY = 'array'; */
    const PAYLOAD_DATA_TYPE_STRING = 'string';

    /**
     * Fonction servant a extraire toutes les information d'une payload.
     * Les payload doivent absolument transiter par cette methode
     *
     * A enrichir avec de nouveau type au fur et a mesure que les besoins évoluent
     *
     * @param array $payload
     * @param string $key
     * @param string|null $type
     * @param bool $nullable
     *
     * @return mixed|null Retourne null ou une valeur correspondant au type renseignée
     */
    public function extractPayloadValue(array $payload, string $key, string $type, bool $nullable = false)
    {
        $value = null;
        // On vérifie que la clé existe et que la valeur associé n'est pas null
        $payloadValid = array_key_exists($key, $payload) && !is_null($payload[$key]);
        $valid = false;

        // PayloadValid assure que la valeur existe, puis on check le type puis on assigne a Value
        if($payloadValid) {
            switch ($type) {
                case self::PAYLOAD_DATA_TYPE_STRING:
                    $valid = is_string($payload[$key]);
                    if($valid){
                        $value = $payload[$key];
                    }
                    break;
                default:
                    $value = null;
                    break;
            }
        }

        // Quand c'est activé, fait bugguer le token JWT

        /* if(!$nullable && $payloadValid){
            throw new InvalidPayloadException("Missing payload information");
        } */

        return $value;

    }

    /**
     * Permet d'extraire une payload dans le cas spécifique d'un User
     *
     * @param array $payload
     *
     * @return array $data
     */
    public function extractUserPayload(array $payload): array
    {
        $data = [];

        // On verifie chaque clé car on peut ne pas vouloir toutes les utiliser
        if (array_key_exists(User::USER_EMAIL_API_KEY, $payload)) {
            $data[User::USER_EMAIL_API_KEY] = $this->extractPayloadValue($payload,User::USER_EMAIL_API_KEY, PayloadManager::PAYLOAD_DATA_TYPE_STRING, false);
        }
        if (array_key_exists(User::USER_FIRST_NAME_API_KEY, $payload)) {
            $data[User::USER_FIRST_NAME_API_KEY] = $this->extractPayloadValue($payload,User::USER_FIRST_NAME_API_KEY, PayloadManager::PAYLOAD_DATA_TYPE_STRING, false);
        }
        if (array_key_exists(User::USER_LAST_NAME_API_KEY, $payload)) {
            $data[User::USER_LAST_NAME_API_KEY] = $this->extractPayloadValue($payload,User::USER_LAST_NAME_API_KEY, PayloadManager::PAYLOAD_DATA_TYPE_STRING, false);
        }
        if (array_key_exists(User::USER_PASSWORD_NAME_API_KEY, $payload)) {
            $data[User::USER_PASSWORD_NAME_API_KEY] = $this->extractPayloadValue($payload,User::USER_PASSWORD_NAME_API_KEY, PayloadManager::PAYLOAD_DATA_TYPE_STRING, false);
        }

        return $data;
    }
}
