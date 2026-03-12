<?php
namespace UesPlay\Infrastructure\Functions;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Blade;

use UesPlay\Domain\Entities\EmailTemplate;
use UesPlay\Domain\Interfaces\IEmailTemplateRepository;
use UesPlay\Domain\Interfaces\ISendEmailFunction;
class SendEmailFunction implements ISendEmailFunction  {
    
    private readonly IEmailTemplateRepository $emailTemplateRepository;
    
    public function __construct(IEmailTemplateRepository $emailTemplateRepository) {
        $this->emailTemplateRepository = $emailTemplateRepository;
    }
    
    public function SendSimpleEmail(string $address, EmailTemplate $template): void {
        $body = Blade::render($template->getContent(), ['user'=>'']);

        Mail::send([], [], function ($message) use ($address,$template, $body) {
            $message->to($address)
                    ->subject($template->getSubject())
                    ->html($body);
        });
    }
}
