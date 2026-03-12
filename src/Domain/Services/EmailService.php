<?php

namespace UesPlay\Domain\Services;

use Illuminate\Support\Collection;
use Exception;
use UesPlay\Domain\Interfaces\ISendEmailFunction;
use UesPlay\Domain\Interfaces\IEmailTemplateRepository;

class EmailService {

    private readonly IEmailTemplateRepository $emailTemplateRepository;
    private readonly ISendEmailFunction $sendEmailFunction;
            
    public function __construct(IEmailTemplateRepository $emailTemplateRepository, 
                                ISendEmailFunction $sendEmailFunction) {
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->sendEmailFunction = $sendEmailFunction;
    }

    public function sendEmail(string $code, $address , Collection $data):void{
         try{

            $template = $this->emailTemplateRepository->findByCode($code);
            $content = $template->getContent();
            $data->each(function (string $value, string $key) use(&$content){
                $content = str_replace($key,$value,$content);
            });
            
            $template->setContent($content);
            $this->sendEmailFunction->SendSimpleEmail($address, $template);
        } catch (Exception $ex) {
            dd($ex);
            throw new Exception($ex->getMessage());
        }
    }
}
