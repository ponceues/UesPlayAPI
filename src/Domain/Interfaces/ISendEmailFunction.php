<?php

namespace UesPlay\Domain\Interfaces;

use UesPlay\Domain\Entities\EmailTemplate;

interface ISendEmailFunction {
    function SendSimpleEmail(string $address, EmailTemplate $template):void;
}
