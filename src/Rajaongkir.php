<?php
/**
 * Advanced RajaOngkir API PHP Library
 *
 * Copyright (C) 2018  Steeve Andrian Salim (steevenz)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) 2018, Steeve Andrian Salim
 * @since          Version 2.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

namespace Steevenz;

// ------------------------------------------------------------------------

use GuzzleHttp\Client;

/**
 * Class Rajaongkir
 * @package Steevenz
 */
class Rajaongkir
{
    /**
     * Constant Account Type
     *
     * @access  public
     * @type    string
     */
    const ACCOUNT_STARTER = 'starter';
    const ACCOUNT_BASIC = 'basic';
    const ACCOUNT_PRO = 'pro';

    /**
     * Rajaongkir::$accountType
     *
     * Rajaongkir Account Type.
     *
     * @access  protected
     * @type    string
     */
    protected $accountType = 'starter';

    /**
     * Rajaongkir::$apiKey
     *
     * Rajaongkir API key.
     *
     * @access  protected
     * @type    string
     */
    protected $apiKey = null;

    /**
     * List of Supported Account Types
     *
     * @access  protected
     * @type    array
     */
    protected $supportedAccountTypes = [
        'starter',
        'basic',
        'pro',
    ];

    /**
     * Supported Couriers
     *
     * @access  protected
     * @type    array
     */
    protected $supportedCouriers = [
        'starter' => [
            'jne',
            'pos',
            'tiki',
        ],
        'basic' => [
            'jne',
            'pos',
            'tiki',
            'pcp',
            'esl',
            'rpx',
        ],
        'pro' => [
            'jne',
            'pos',
            'tiki',
            'rpx',
            'esl',
            'pcp',
            'pandu',
            'wahana',
            'sicepat',
            'jnt',
            'pahala',
            'cahaya',
            'sap',
            'jet',
            'indah',
            'slis',
            'expedito*',
            'dse',
            'first',
            'ncs',
            'star',
        ],
    ];

    /**
     * Rajaongkir::$supportedWaybills
     *
     * Rajaongkir supported couriers waybills.
     *
     * @access  protected
     * @type    array
     */
    protected $supportedWayBills = [
        'starter' => [],
        'basic' => [
            'jne',
        ],
        'pro' => [
            'jne',
            'pos',
            'tiki',
            'pcp',
            'rpx',
            'wahana',
            'sicepat',
            'jnt',
            'sap',
            'jet',
            'dse',
            'first',
        ],
    ];

    /**
     * Rajaongkir::$couriersList
     *
     * Rajaongkir courier list.
     *
     * @access  protected
     * @type array
     */
    protected $couriersList = [
        'jne' => 'Jalur Nugraha Ekakurir (JNE)',
        'pos' => 'POS Indonesia (POS)',
        'tiki' => 'Citra Van Titipan Kilat (TIKI)',
        'pcp' => 'Priority Cargo and Package (PCP)',
        'esl' => 'Eka Sari Lorena (ESL)',
        'rpx' => 'RPX Holding (RPX)',
        'pandu' => 'Pandu Logistics (PANDU)',
        'wahana' => 'Wahana Prestasi Logistik (WAHANA)',
        'sicepat' => 'SiCepat Express (SICEPAT)',
        'jnt' => 'J&T Express (J&T)',
        'pahala' => 'Pahala Kencana Express (PAHALA)',
        'cahaya' => 'Cahaya Logistik (CAHAYA)',
        'sap' => 'SAP Express (SAP)',
        'jet' => 'JET Express (JET)',
        'indah' => 'Indah Logistic (INDAH)',
        'slis' => 'Solusi Express (SLIS)',
        'expedito*' => 'Expedito*',
        'dse' => '21 Express (DSE)',
        'first' => 'First Logistics (FIRST)',
        'ncs' => 'Nusantara Card Semesta (NCS)',
        'star' => 'Star Cargo (STAR)',
    ];

    /**
     * Rajaongkir::$response
     *
     * Rajaongkir response.
     *
     * @access  protected
     * @type    mixed
     */
    protected $response;

    /**
     * Rajaongkir::$errors
     *
     * Rajaongkir errors.
     *
     * @access  protected
     * @type    mixed
     */
    public $errors;

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::__construct
     *
     * @access  public
     * @throws  \InvalidArgumentException
     */
    public function __construct($apiKey = null, $accountType = null)
    {
        if (isset($apiKey)) {
            if (is_array($apiKey)) {
                if (isset($apiKey['api_key'])) {
                    $this->apiKey = $apiKey['api_key'];
                }

                if (isset($apiKey['account_type'])) {
                    $accountType = $apiKey['account_type'];
                }
            } elseif (is_string($apiKey)) {
                $this->apiKey = $apiKey;
            }
        }

        if (isset($accountType)) {
            $this->setAccountType($accountType);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::setApiKey
     *
     * Set Rajaongkir API Key.
     *
     * @param   string $apiKey Rajaongkir API Key
     *
     * @access  public
     * @return  static
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::setAccountType
     *
     * Set Rajaongkir account type.
     *
     * @param   string $accountType RajaOngkir Account Type, can be starter, basic or pro
     *
     * @access  public
     * @return  static
     * @throws  \InvalidArgumentException
     */
    public function setAccountType($accountType)
    {
        $accountType = strtolower($accountType);

        if (in_array($accountType, $this->supportedAccountTypes)) {
            $this->accountType = $accountType;
        } else {
            throw new \InvalidArgumentException('Rajaongkir: Invalid Account Type');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::request
     *
     * Curl request API caller.
     *
     * @param string $path
     * @param array $params
     * @param string $type
     *
     * @access  protected
     * @return  array|bool Returns FALSE if failed.
     */
    protected function request($path, $params = [], $type = 'GET')
    {
        $apiUrl = 'https://api.rajaongkir.com';

        switch ($this->accountType) {
            default:
            case 'starter':
                $path = 'starter/' . $path;
                break;

            case 'basic':
                $path = 'basic/' . $path;
                break;

            case 'pro':
                $apiUrl = 'https://pro.rajaongkir.com';
                $path = 'api/' . $path;
                break;
        }

        $uri = $apiUrl . '/' . $path;
        $client = new Client();

        try {
            switch ($type) {
                default:
                case 'GET':
                    $response = $client->request('GET', $uri, [
                        'headers' => ['key' => $this->apiKey,],
                        'query' => $params
                    ]);
                    break;

                case 'POST':
                    $response = $client->request('POST', $uri, [
                        'headers' => ['key' => $this->apiKey,],
                        'form_params' => $params
                    ]);
                    break;
            }

            if ($response->getStatusCode() !== 200) {
                $this->errors[$response->getStatusCode()] = $response->getReasonPhrase();
                return false;
            }

            $body = json_decode((string)$response->getBody(), true);
            $body = (array)$body['rajaongkir'];
            $status = $body['status'];

            if ($status['code'] == 200) {
                if (isset($body['results'])) {
                    if (count($body['results']) == 1 && isset($body['results'][0])) {
                        return $body['results'][0];
                    } elseif (count($body['results'])) {
                        return $body['results'];
                    }
                } elseif (isset($body['result'])) {
                    return $body['result'];
                }
            } else {
                $this->errors[$status['code']] = $status['description'];
                return false;
            }

        } catch (\Exception $e) {
            $this->errors[$e->getCode()] = $e->getMessage();
            return false;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getCouriersList
     *
     * Get list of supported couriers.
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getCouriersList()
    {
        return $this->couriersList;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getProvinces
     *
     * Get list of provinces.
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getProvinces()
    {
        return $this->request('province');
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getProvince
     *
     * Get detail of single province.
     *
     * @param   int $idProvince Province ID
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getProvince($idProvince)
    {
        return $this->request('province', ['id' => $idProvince]);
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getCities
     *
     * Get list of province cities.
     *
     * @param   int $idProvince Province ID
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getCities($idProvince = null)
    {
        $params = [];

        if (!is_null($idProvince)) {
            $params['province'] = $idProvince;
        }

        return $this->request('city', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getCity
     *
     * Get detail of single city.
     *
     * @param   int $idCity City ID
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getCity($idCity)
    {
        return $this->request('city', ['id' => $idCity]);
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getSubdistricts
     *
     * Get list of city subdisctricts.
     *
     * @param   int $idCity City ID
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getSubdistricts($idCity)
    {
        if ($this->accountType === 'starter') {
            $this->errors[302] = 'Unsupported Subdistricts Request. Tipe akun starter tidak mendukung hingga tingkat kecamatan.';
            return false;
        } elseif ($this->accountType === 'basic') {
            $this->errors[302] = 'Unsupported Subdistricts Request. Tipe akun basic tidak mendukung hingga tingkat kecamatan.';
            return false;
        }

        return $this->request('subdistrict', ['city' => $idCity]);
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getSubdistrict
     *
     * Get detail of single subdistrict.
     *
     * @param   int $idSubdistrict Subdistrict ID
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getSubdistrict($idSubdistrict)
    {
        if ($this->accountType === 'starter') {
            $this->errors[302] = 'Unsupported Subdistricts Request. Tipe akun starter tidak mendukung hingga tingkat kecamatan.';

            return false;
        } elseif ($this->accountType === 'basic') {
            $this->errors[302] = 'Unsupported Subdistricts Request. Tipe akun basic tidak mendukung hingga tingkat kecamatan.';

            return false;
        }

        return $this->request('subdistrict', ['id' => $idSubdistrict]);
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getInternationalOrigins
     *
     * Get list of supported international origins.
     *
     * @param   int $idProvince Province ID
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getInternationalOrigins($idProvince = null)
    {
        if ($this->accountType === 'starter') {
            $this->errors[301] = 'Unsupported International Origin Request. Tipe akun starter tidak mendukung tingkat international.';

            return false;
        }

        $params = [];

        if (isset($idProvince)) {
            $params['province'] = $idProvince;
        }

        return $this->request('v2/internationalOrigin', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getInternationalOrigin
     *
     * Get list of supported international origins by city and province.
     *
     * @param   int $idCity City ID
     * @param   int $idProvince Province ID
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getInternationalOrigin($idCity = null, $idProvince = null)
    {
        if ($this->accountType === 'starter') {
            $this->errors[301] = 'Unsupported International Origin Request. Tipe akun starter tidak mendukung tingkat international.';

            return false;
        }

        if (isset($idCity)) {
            $params['id'] = $idCity;
        }

        if (isset($idProvince)) {
            $params['province'] = $idProvince;
        }

        return $this->request('v2/internationalOrigin', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getInternationalDestinations
     *
     * Get list of international destinations.
     *
     * @param   int $id_country Country ID
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getInternationalDestinations()
    {
        if ($this->accountType === 'starter') {
            $this->errors[301] = 'Unsupported International Destination Request. Tipe akun starter tidak mendukung tingkat international.';

            return false;
        }

        return $this->request('v2/internationalDestination');
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getInternationalDestination
     *
     * Get International Destination
     *
     * @param   int $idCountry Country ID
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getInternationalDestination($idCountry = null)
    {
        if ($this->accountType === 'starter') {
            $this->errors[301] = 'Unsupported International Destination Request. Tipe akun starter tidak mendukung tingkat international.';

            return false;
        }

        $params = [];

        if (isset($idCountry)) {
            $params['id'] = $idCountry;
        }

        return $this->request('v2/internationalDestination', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getCost
     *
     * Get cost calculation.
     *
     * @example
     * $rajaongkir->getCost(
     *      ['city' => 1],
     *      ['subdistrict' => 12],
     *      ['weight' => 100, 'length' => 100, 'width' => 100, 'height' => 100, 'diameter' => 100],
     *      'jne'
     * );
     *
     * @see      http://rajaongkir.com/dokumentasi/pro
     *
     * @param array $origin City, District or Subdistrict Origin
     * @param array $destination City, District or Subdistrict Destination
     * @param array $metrics Array of Specification
     *                                  weight      int     weight in gram (required)
     *                                  length      number  package length dimension
     *                                  width       number  package width dimension
     *                                  height      number  package height dimension
     *                                  diameter    number  package diameter
     * @param string $courier Courier Code
     *
     * @access   public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getCost(array $origin, array $destination, $metrics, $courier)
    {
        $params['courier'] = strtolower($courier);

        $params['originType'] = strtolower(key($origin));
        $params['destinationType'] = strtolower(key($destination));

        if ($params['originType'] !== 'city') {
            $params['originType'] = 'subdistrict';
        }

        if (!in_array($params['destinationType'], ['city', 'country'])) {
            $params['destinationType'] = 'subdistrict';
        }

        if (is_array($metrics)) {
            if (!isset($metrics['weight']) AND
                isset($metrics['length']) AND
                isset($metrics['width']) AND
                isset($metrics['height'])
            ) {
                $metrics['weight'] = (($metrics['length'] * $metrics['width'] * $metrics['height']) / 6000) * 1000;
            } elseif (isset($metrics['weight']) AND
                isset($metrics['length']) AND
                isset($metrics['width']) AND
                isset($metrics['height'])
            ) {
                $weight = (($metrics['length'] * $metrics['width'] * $metrics['height']) / 6000) * 1000;

                if ($weight > $metrics['weight']) {
                    $metrics['weight'] = $weight;
                }
            }

            foreach ($metrics as $key => $value) {
                $params[$key] = $value;
            }
        } elseif (is_numeric($metrics)) {
            $params['weight'] = $metrics;
        }

        switch ($this->accountType) {
            case 'starter':

                if ($params['destinationType'] === 'country') {
                    $this->errors[301] = 'Unsupported International Destination. Tipe akun starter tidak mendukung pengecekan destinasi international.';

                    return false;
                } elseif ($params['originType'] === 'subdistrict' OR $params['destinationType'] === 'subdistrict') {
                    $this->errors[302] = 'Unsupported Subdistrict Origin-Destination. Tipe akun starter tidak mendukung pengecekan ongkos kirim sampai kecamatan.';

                    return false;
                }

                if (!isset($params['weight']) AND
                    isset($params['length']) AND
                    isset($params['width']) AND
                    isset($params['height'])
                ) {
                    $this->errors[304] = 'Unsupported Dimension. Tipe akun starter tidak mendukung pengecekan biaya kirim berdasarkan dimensi.';

                    return false;
                } elseif (isset($params['weight']) AND $params['weight'] > 30000) {
                    $this->errors[305] = 'Unsupported Weight. Tipe akun starter tidak mendukung pengecekan biaya kirim dengan berat lebih dari 30000 gram (30kg).';

                    return false;
                }

                if (!in_array($params['courier'], $this->supportedCouriers[$this->accountType])) {
                    $this->errors[303] = 'Unsupported Courier. Tipe akun starter tidak mendukung pengecekan biaya kirim dengan kurir ' . $this->couriersList[$courier] . '.';

                    return false;
                }

                break;

            case 'basic':

                if ($params['originType'] === 'subdistrict' OR $params['destinationType'] === 'subdistrict') {
                    $this->errors[302] = 'Unsupported Subdistrict Origin-Destination. Tipe akun basic tidak mendukung pengecekan ongkos kirim sampai kecamatan.';

                    return false;
                }

                if (!isset($params['weight']) AND
                    isset($params['length']) AND
                    isset($params['width']) AND
                    isset($params['height'])
                ) {
                    $this->errors[304] = 'Unsupported Dimension. Tipe akun basic tidak mendukung pengecekan biaya kirim berdasarkan dimensi.';

                    return false;
                } elseif (isset($params['weight']) AND $params['weight'] > 30000) {
                    $this->errors[305] = 'Unsupported Weight. Tipe akun basic tidak mendukung pengecekan biaya kirim dengan berat lebih dari 30000 gram (30kg).';

                    return false;
                } elseif (isset($params['weight']) AND $params['weight'] < 30000) {
                    unset($params['length'], $params['width'], $params['height']);
                }

                if (!in_array($params['courier'], $this->supportedCouriers[$this->accountType])) {
                    $this->errors[303] = 'Unsupported Courier. Tipe akun basic tidak mendukung pengecekan biaya kirim dengan kurir ' . $this->couriersList[$courier] . '.';
                    return false;
                }

                break;
        }

        $params['origin'] = $origin[key($origin)];
        $params['destination'] = $destination[key($destination)];

        $path = key($destination) === 'country' ? 'internationalCost' : 'cost';

        return $this->request($path, $params, 'POST');
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getWaybill
     *
     * Get detail of waybill.
     *
     * @param   int $idWaybill Receipt ID
     * @param   null|string $courier Courier Code
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getWaybill($idWaybill, $courier)
    {
        $courier = strtolower($courier);

        if (in_array($courier, $this->supportedWayBills[$this->accountType])) {
            return $this->request('waybill', [
                'key' => $this->apiKey,
                'waybill' => $idWaybill,
                'courier' => $courier,
            ], 'POST');
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getCurrency
     *
     * Get Rajaongkir currency.
     *
     * @access  public
     * @return  array|bool Returns FALSE if failed.
     */
    public function getCurrency()
    {
        if ($this->accountType !== 'starter') {
            return $this->request('currency');
        }

        $this->errors[301] = 'Unsupported Get Currency. Tipe akun starter tidak mendukung pengecekan currency.';

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getSupportedCouriers
     *
     * Gets list of supported couriers by your account.
     *
     * @return array|bool Returns FALSE if failed.
     */
    public function getSupportedCouriers()
    {
        if (isset($this->supportedCouriers[$this->accountType])) {
            return $this->supportedCouriers[$this->accountType];
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Rajaongkir::getSupportedWayBills
     *
     * Gets list of supported way bills based on account type.
     *
     * @return array|bool Returns FALSE if failed.
     */
    public function getSupportedWayBills()
    {
        if (isset($this->supportedWayBills[$this->accountType])) {
            return $this->supportedWayBills[$this->accountType];
        }

        return false;
    }
}
