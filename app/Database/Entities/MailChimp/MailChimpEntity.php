<?php
declare(strict_types=1);

namespace App\Database\Entities\MailChimp;

use App\Database\Entities\Entity;
use EoneoPay\Utils\Str;

abstract class MailChimpEntity extends Entity
{
    /**
     * Get validation rules for mailchimp entity.
     *
     * @return array
     */
    abstract public function getValidationRules(): array;

    /**
     * Get array representation of entity.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $str = new Str();

        foreach (\get_object_vars($this) as $property => $value) {
            $array[$str->snake($property)] = $value;
        }

        return $array;
    }

    /**
     * Get mailchimp array representation of entity.
     *
     * @return array
     */
    public function toMailChimpArray(): array
    {
        $array = [];

        foreach ($this->toArray() as $property => $value) {
            if ($value === null) {
                continue;
            }

            $array[$property] = $value;
        }

        return $array;
    }
}
