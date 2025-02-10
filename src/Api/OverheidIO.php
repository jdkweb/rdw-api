<?php

namespace Jdkweb\Rdw\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Lang;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Exceptions\RdwException;

class OverheidIO extends Rdw implements RdwApi
{
    protected string $base_uri = "https://api.overheid.io";

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set HTTP Client
     *
     * @return void
     */
    final public function setClient():void
    {
        $this->client = new Client([
            'base_uri' => rtrim($this->base_uri, "/")."/",
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'ovio-api-key' => config("rdw-api.rdw_api_key"),
            ]
        ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fetch the Rdw results
     *
     * @return string|array
     * @throws RdwException
     */
    final public function fetch():string|array
    {
        $response = $this->getRequest();

        //$response = json_decode('{"jaar_laatste_registratie_tellerstand":2025,"aantal_cilinders":4,"zuinigheidsclassificatie":"E","typegoedkeuringsnummer":"e2*2001\/116*0327*20",":updated_at":"2025-01-07T07:16:51.426Z","datum_eerste_toelating":"2008-12-01","eerste_kleur":"BLAUW","datum_eerste_tenaamstelling_in_nederland":"2015-12-30","code_toelichting_tellerstandoordeel":"05","europese_voertuigcategorie":"M1","type":"R","export_indicator":false,"wam_verzekerd":true,":created_at":"2024-05-28T11:41:18.407Z",":id":"row-eyp4.paih-8y3w","cilinderinhoud":1598,"kenteken":"HX084V","maximum_massa_samenstelling":2600,"variant":"BRCB","vervaldatum_apk":"2026-01-07","toegestane_maximum_massa_voertuig":1675,"maximum_trekken_massa_geremd":1200,"brandstof":[{"brandstofverbruik_gecombineerd":7.5,"geluidsniveau_stationair":82,"brandstofverbruik_stad":10,":updated_at":"2025-01-21T14:48:38.495Z","geluidsniveau_rijdend":71,":created_at":"2025-01-21T14:48:38.495Z","brandstof_volgnummer":1,"emissiecode_omschrijving":"4","nettomaximumvermogen":82,"brandstof_omschrijving":"Benzine","uitlaatemissieniveau":"EURO 4",":id":"row-jmuw.ivwk.m4i9","co2_uitstoot_gecombineerd":179,"brandstofverbruik_buiten":"6.10","toerental_geluidsniveau":4500,"milieuklasse_eg_goedkeuring_licht":"70\/220*2003\/76B"}],"merk":"RENAULT","kentekenplaat":"HX-084-V","carrosserie":[{"carrosserie_volgnummer":1,"carrosserietype":"AB","type_carrosserie_europese_omschrijving":"Hatchback"}],"uitvoering":"BRCB0A","taxi_indicator":false,"tellerstandoordeel":"Geen oordeel","openstaande_terugroepactie_indicator":false,"aantal_zitplaatsen":5,"bruto_bpm":5134,"technische_max_massa_voertuig":1675,"voertuigsoort":"Personenauto","handelsbenaming":"CLIO","as":[{"wettelijk_toegestane_maximum_aslast":924,"plaatscode_as":"V","aantal_assen":2,"as_nummer":1,"spoorbreedte":147},{"wettelijk_toegestane_maximum_aslast":870,"plaatscode_as":"A","aantal_assen":2,"as_nummer":2,"spoorbreedte":145}],"massa_rijklaar":1265,"plaats_chassisnummer":"r. in kofferruimte by reservewiel","inrichting":"hatchback","vermogen_massarijklaar":0.06,"aantal_wielen":4,"datum_tenaamstelling":"2015-12-30","massa_ledig_voertuig":1165,"tenaamstellen_mogelijk":true,"maximum_massa_trekken_ongeremd":535,"wielbasis":258,"sidecode":9,"aantal_deuren":4,"_links":{"self":{"href":"\/voertuiggegevens\/hx084v?ovio-api-key=5c70860a034c9a8fe8f9dd83a83d08c073563bab995962e834bacf337732ca65"}}}',true);
        //$response = json_decode('{"typegoedkeuringsnummer":"e4*2018\/858*00074*02","datum_eerste_toelating":"2023-04-13","laadvermogen":885,"code_toelichting_tellerstandoordeel":"04","breedte":206,"type":"SV63C","export_indicator":false,"wam_verzekerd":true,"maximum_massa_samenstelling":4250,"registratie_datum_goedkeuring_afschrijvingsmoment_bpm":"2023-02-16","variant":"6620","hoogte_voertuig":253,"aanhangwagen_middenas_geremd":1500,"kentekenplaat":"VTV-69-T","carrosserie":[{"specificatie":[{"carrosseriecode":3,"carrosserie_voertuig_nummer_code_volgnummer":1,"carrosserie_volgnummer":1,"carrosserie_voertuig_nummer_europese_omschrijving":"Gesloten opbouw"}],"carrosserie_volgnummer":1,"carrosserietype":"BB","type_carrosserie_europese_omschrijving":"Bestelwagen"}],"taxi_indicator":false,"aantal_zitplaatsen":3,"voertuigsoort":"Bedrijfsauto","catalogusprijs":88366,"as":[{"wettelijk_toegestane_maximum_aslast":1950,"hefas":false,"plaatscode_as":"V","technisch_toegestane_maximum_aslast":1950,"aantal_assen":2,"aangedreven_as":true,"afstand_tot_volgende_as_voertuig":376,"as_nummer":1,"spoorbreedte":173},{"wettelijk_toegestane_maximum_aslast":1900,"hefas":false,"plaatscode_as":"A","technisch_toegestane_maximum_aslast":1900,"aantal_assen":2,"aangedreven_as":false,"as_nummer":2,"spoorbreedte":176}],"massa_rijklaar":2715,"vermogen_massarijklaar":0.03,"aantal_wielen":4,"datum_tenaamstelling":"2023-10-17","wielbasis":376,"sidecode":11,"aantal_deuren":4,"jaar_laatste_registratie_tellerstand":2023,":updated_at":"2024-12-20T15:08:03.060Z","datum_eerste_tenaamstelling_in_nederland":"2023-04-13","maximale_constructiesnelheid":100,"europese_voertuigcategorie":"N1",":created_at":"2024-05-28T11:41:18.407Z",":id":"row-pazr-2r6a-upfj","kenteken":"VTV69T","vervaldatum_apk":"2027-04-13","toegestane_maximum_massa_voertuig":3500,"brandstof":[{"nominaal_continu_maximumvermogen":70,":updated_at":"2025-01-21T14:48:38.495Z","geluidsniveau_rijdend":69,"netto_max_vermogen_elektrisch":150,"actie_radius_enkel_elektrisch_stad_wltp":353,":created_at":"2025-01-21T14:48:38.495Z","brandstof_volgnummer":1,"emissiecode_omschrijving":"Z","brandstof_omschrijving":"Elektriciteit","uitlaatemissieniveau":"AX",":id":"row-sft3-mbgm.rvfm","elektrisch_verbruik_enkel_elektrisch_wltp":321,"actie_radius_enkel_elektrisch_wltp":296,"milieuklasse_eg_goedkeuring_licht":"715\/2007*2018\/1832AX"}],"merk":"MAXUS","uitvoering":"033336P","tellerstandoordeel":"Onlogisch","openstaande_terugroepactie_indicator":false,"lengte":594,"technische_max_massa_voertuig":3500,"handelsbenaming":"MAXUS EDELIVER 9","inrichting":"gesloten opbouw","massa_ledig_voertuig":2615,"tenaamstellen_mogelijk":true,"maximum_massa_trekken_ongeremd":750,"_links":{"self":{"href":"\/voertuiggegevens\/VTV69T"}}}',true);
        //$response = json_decode('{"aantal_staanplaatsen":58,"aerodynamische_voorziening_of_uitrusting":"N","datum_eerste_toelating":"2023-07-21","laadvermogen":6540,"datum_eerste_tenaamstelling_in_nederland":"2023-07-21","code_toelichting_tellerstandoordeel":"NG","maximale_constructiesnelheid":80,"breedte":255,"europese_voertuigcategorie":"M3","type":"407E5","export_indicator":false,"wam_verzekerd":true,"registratie_datum_goedkeuring(afschrijvingsmoment_bpm)_dt":"06\/20\/2023 12:00:00 AM","kenteken":"52BVL9","vervaldatum_apk":"2025-09-21","toegestane_maximum_massa_voertuig":19500,"brandstof":[{":created_at":"2025-01-21T14:48:38.495Z","brandstof_volgnummer":1,"emissiecode_omschrijving":"Z","nominaal_continu_maximumvermogen":232,"brandstof_omschrijving":"Elektriciteit",":id":"row-babr.pwbq.seiu",":updated_at":"2025-01-21T14:48:38.495Z","geluidsniveau_rijdend":74,"netto_max_vermogen_elektrisch":280}],"hoogte_voertuig":324,"merk":"VDL","kentekenplaat":"52-BVL-9","carrosserie":[{"voertuigklasse":[{"voertuigklasse":1,"carrosserie_volgnummer":1,"carrosserie_klasse_volgnummer":1,"voertuigklasse_omschrijving":"Klasse I"}],"carrosserie_volgnummer":1,"carrosserietype":"CE","type_carrosserie_europese_omschrijving":"Enkeldeksvoertuig met lage vloer"}],"taxi_indicator":false,"openstaande_terugroepactie_indicator":false,"aantal_zitplaatsen":37,"lengte":1220,"technische_max_massa_voertuig":20000,"voertuigsoort":"Bus","handelsbenaming":"CITEA LF-122\/ ELECTRIC","as":[{"wettelijk_toegestane_maximum_aslast":8000,"plaatscode_as":"V","technisch_toegestane_maximum_aslast":8000,"aantal_assen":2,"aangedreven_as":false,"afstand_tot_volgende_as_voertuig":635,"as_nummer":1,"spoorbreedte":215},{"wettelijk_toegestane_maximum_aslast":11500,"plaatscode_as":"A","technisch_toegestane_maximum_aslast":12000,"aantal_assen":2,"weggedrag_code":"G","aangedreven_as":true,"as_nummer":2,"spoorbreedte":189}],"additionele_massa_alternatieve_aandrijving":"2463","massa_rijklaar":13060,"inrichting":"bus","vermogen_massarijklaar":0.02,"aantal_wielen":6,"datum_tenaamstelling":"2023-07-21","massa_ledig_voertuig":12960,"tenaamstellen_mogelijk":true,"wielbasis":635,"sidecode":7,"aantal_deuren":5,"registratie_datum_goedkeuring(afschrijvingsmoment_bpm)":"20230620","aantal_rolstoelplaatsen":1,"_links":{"self":{"href":"\/voertuiggegevens\/52BVL9"}}}',true);

        // Split result into rdw datasets
        $result = $this->toDataSets($response);

        foreach ($this->endpoints as $endpoint) {
            // Check endpoint exists
            if (!$endpoint instanceof Endpoints) {
                continue;
            }

            // Remove specific overheid.io data-fields
            $result[$endpoint->name] = array_filter($result[$endpoint->name], function ($key) {
                return (substr($key, 0, 1) !== ":") && $key !== "_links";
            }, ARRAY_FILTER_USE_KEY);

            // add licenseplate when result
            if (count($result[$endpoint->name]) > 0) {
                $result[$endpoint->name][Lang::get('rdw-api::vehicle.kenteken', [], 'nl')] = $this->license;
            }

            $this->result[$endpoint->name] = $result[$endpoint->name];
        }

        // Translation when needed
        return $this->translateOutput($this->result);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Split data into RDW datasets
     *
     * @param  array  $response
     * @return array
     */
    final protected function toDataSets(array $response): array
    {
        $result = [];
        $result[Endpoints::FUEL->name] = $response['brandstof'][0];
        unset($response['brandstof']);
        $result[Endpoints::BODYWORK_SPECIFIC->name] = [];
        $result[Endpoints::VEHICLE_CLASS->name] = [];
        if (isset($response['carrosserie'][0]['specificatie'][0])) {
            $result[Endpoints::BODYWORK_SPECIFIC->name] = $response['carrosserie'][0]['specificatie'][0];
            unset($response['carrosserie'][0]['specificatie']);
        }
        if (isset($response['carrosserie'][0]['voertuigklasse'][0])) {
            $result[Endpoints::VEHICLE_CLASS->name] = $response['carrosserie'][0]['voertuigklasse'][0];
            unset($response['carrosserie'][0]['voertuigklasse']);
        }
        $result[Endpoints::BODYWORK->name] = $response['carrosserie'][0];
        unset($response['carrosserie']);
        $result[Endpoints::AXLES->name] = $response['as'];
        unset($response['as']);
        $result[Endpoints::VEHICLE->name] = $response;

        return $result;
    }

    //------------------------------------------------------------------------------------------------------------------

    final protected function getRequest(): ?array
    {
        try {
            $response = $this->client->get("/voertuiggegevens/" . $this->license);
            $statusCode = $response->getStatusCode() ?? 404;

            $responseBody = (string) $response->getBody();

            $result = json_decode($responseBody, true) ?? [];

            if (count($result) > 1) {
                return $result;
            }

            return null;
        } catch (ClientException $e) {
            throw new RdwException(__('rdw-api::errors.endpoint_error', [
                'class' => self::class,
                'message' => $e->getMessage(),
                'endpoint_name' => 'nvt',
                'endpoint_value' => ''
            ]));
        }
    }
}
