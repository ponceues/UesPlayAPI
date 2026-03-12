<?php

namespace UesPlay\Domain\Interfaces;

use UesPlay\Domain\Entities\EmailTemplate;

interface IEmailTemplateRepository {
    function findByCode(string $code): EmailTemplate;
}
