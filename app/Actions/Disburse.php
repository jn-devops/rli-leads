<?php

namespace App\Actions;

use Illuminate\Http\Client\ConnectionException;
use Propaganistas\LaravelPhone\PhoneNumber;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Http;
use Homeful\KwYCCheck\Data\LeadData;
use Homeful\KwYCCheck\Models\Lead;
use Illuminate\Support\Str;

class Disburse
{
    use AsAction;

    protected LeadData $data;
    public function handle(Lead $lead, int $amount): bool
    {
        $this->data = LeadData::fromModel($lead);
        $response = $this->disburse($amount);
        logger($response->body());

        return $response->ok();
    }

    public function asJob(Lead $lead, int $amount): void
    {
        $this->handle($lead, $amount);
    }

    /**
     * @throws ConnectionException
     */
    protected function disburse(int $amount): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $url = config('leads.disbursement.server.url');
        $headers = [
            'Authorization' => 'Bearer ' . config('leads.disbursement.server.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        $reference = $this->generateReferenceCode();
        $bank = $this->getBankCode();
        $account_number = $this->getMobile();
        $via = $this->getTransferVia();
        $body = compact('reference','bank', 'account_number', 'via', 'amount');

        return Http::withHeaders($headers)->post($url, $body);
    }

    protected function getMobile(): string
    {
        return (new PhoneNumber($this->data->checkin->inputs->mobile, 'PH'))
            ->formatForMobileDialingInCountry('PH');
    }

    protected function generateReferenceCode(): string
    {
        $campaign_codes = [];
        $code = $this->data->checkin->campaign->code;
        $code = Str::uuid()->toString();
        preg_match('/(.*)-(.*)-(.*)-(.*)-(.*)/', $code, $campaign_codes);
        $mobile = $this->getMobile();
        $reference_code = $campaign_codes[2] . '-' . $mobile;
        logger('reference = ' . $reference_code);

        return $reference_code;
    }

    protected function getBankCode(): string
    {
        $bank_code = config('leads.disbursement.bank.code');
        logger('bank_code = ' . $bank_code);

        return $bank_code;
    }

    protected function getTransferVia(): string
    {
        $transfer_mechanism = config('leads.disbursement.bank.via');
        logger('via = ' . $transfer_mechanism);

        return $transfer_mechanism;
    }
}
