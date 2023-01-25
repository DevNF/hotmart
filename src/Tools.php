<?php

namespace Fuganholi\Hotmart;

use Exception;

/**
 * Classe Tools
 *
 * Classe responsável pela comunicação com a API do Hotmart
 *
 * @category  Hotmart
 * @package   Fuganholi\Hotmart\Tools
 * @author    Diego Almeida <diego.feres82 at gmail dot com>
 * @copyright 2022 Hotmart
 * @license   https://opensource.org/licenses/MIT MIT
 */
class Tools
{
    /**
     * URL base para comunicação com a API
     *
     * @var array
     */
    private static $API_URL = [
        1 => 'https://developers.hotmart.com/payments/api/v1',
        2 => 'https://sandbox.hotmart.com/payments/api/v1',
    ];

    /**
     * URL base para autenticação com a API
     *
     * @var string
     */
    private static $AUTH_URL = 'https://api-sec-vlc.hotmart.com/security/oauth';

    /**
     * Variável responsável por armazenar os dados a serem utilizados para comunicação com a API
     * Dados como token, ambiente(produção ou homologação) e debug(true|false)
     *
     * @var array
     */
    private $config = [
        'client_id' => '',
        'client_secret' => '',
        'basic' => '',
        'token' => '',
        'environment' => '',
        'authenticating' => false,
        'debug' => false,
        'upload' => false,
        'decode' => true
    ];

    /**
     * Metodo contrutor da classe
     *
     * @param string $token Token utilizado para comunicação com a hotmart
     * @param boolean $environment Define o ambiente: 1 - Produção, 2 - Sandbox
     */
    public function __construct(string $token = '', int $environment = 1)
    {
        $this->setToken($token);
        $this->setEnvironment($environment);
    }

    /**
     * Define se a classe está sendo autenticada
     *
     * @param bool $authenticating Boleano para definir se é autenticação ou não
     *
     * @access public
     * @return void
     */
    private function setAuthenticating(bool $authenticating) :void
    {
        $this->config['authenticating'] = $authenticating;
    }

    /**
     * Define se a classe realizará um upload
     *
     * @param bool $isUpload Boleano para definir se é upload ou não
     *
     * @access public
     * @return void
     */
    public function setUpload(bool $isUpload) :void
    {
        $this->config['upload'] = $isUpload;
    }

    /**
     * Define se a classe realizará o decode do retorno
     *
     * @param bool $decode Boleano para definir se é decode ou não
     *
     * @access public
     * @return void
     */
    public function setDecode(bool $decode) :void
    {
        $this->config['decode'] = $decode;
    }

    /**
     * Função responsável por definir se está em modo de debug ou não a comunicação com a API
     * Utilizado para pegar informações da requisição
     *
     * @param bool $isDebug Boleano para definir se é produção ou não
     *
     * @access public
     * @return void
     */
    public function setDebug(bool $isDebug) :void
    {
        $this->config['debug'] = $isDebug;
    }

    /**
     * Função responsável por definir o client_id a ser utilizado para comunicação com a API
     *
     * @param string $client_id ClientID para autenticação na API
     *
     * @access public
     * @return void
     */
    public function setClientId(string $client_id) :void
    {
        $this->config['client_id'] = $client_id;
    }

    /**
     * Função responsável por definir o client_secret a ser utilizado para comunicação com a API
     *
     * @param string $client_secret ClientSecret para autenticação na API
     *
     * @access public
     * @return void
     */
    public function setClientSecret(string $client_secret) :void
    {
        $this->config['client_secret'] = $client_secret;
    }

    /**
     * Função responsável por definir o basci token a ser utilizado para autenticação com a API
     *
     * @param string $token Token para autenticação na API
     *
     * @access public
     * @return void
     */
    public function setBasic(string $token) :void
    {
        $this->config['basic'] = str_replace(['Basic ', 'basic '], '', $token);
    }

    /**
     * Função responsável por definir o token a ser utilizado para comunicação com a API
     *
     * @param string $token Token para autenticação na API
     *
     * @access public
     * @return void
     */
    public function setToken(string $token) :void
    {
        $this->config['token'] = str_replace(['Bearer ', 'bearer '], '', $token);
    }

    /**
     * Função responsável por setar o ambiente utilizado na API
     *
     * @param int $environment Ambiente API (1 - Produção | 2 - Sandbox)
     *
     * @access public
     * @return void
     */
    public function setEnvironment(int $environment) :void
    {
        if (in_array($environment, [1, 2])) {
            $this->config['environment'] = $environment;
        }
    }

    /**
     * Recupera se é upload ou não
     *
     *
     * @access public
     * @return bool
     */
    public function getUpload() : bool
    {
        return $this->config['upload'];
    }

    /**
     * Recupera se faz decode ou não
     *
     *
     * @access public
     * @return bool
     */
    public function getDecode() : bool
    {
        return $this->config['decode'];
    }

    /**
     * Retorna o client_id utilizado para comunicação com a API
     *
     * @access public
     * @return string
     */
    public function getClientId() :string
    {
        return $this->config['client_id'];
    }

    /**
     * Retorna o client_secret utilizado para comunicação com a API
     *
     * @access public
     * @return string
     */
    public function getClientSecret() :string
    {
        return $this->config['client_secret'];
    }

    /**
     * Retorna o token utilizado para autenticação com a API
     *
     * @access public
     * @return string
     */
    public function getBasic() :string
    {
        return $this->config['basic'];
    }

    /**
     * Retorna o token utilizado para comunicação com a API
     *
     * @access public
     * @return string
     */
    public function getToken() :string
    {
        return $this->config['token'];
    }

    /**
     * Recupera o ambiente setado para comunicação com a API
     *
     * @access public
     * @return int
     */
    public function getEnvironment() :int
    {
        return $this->config['environment'];
    }

    /**
     * Retorna os cabeçalhos padrão para comunicação com a API
     *
     * @access private
     * @return array
     */
    private function getDefaultHeaders() :array
    {
        $headers = [
            'Accept: application/json'
        ];

        if ($this->config['authenticating']) {
            $headers[] = 'Authorization: Basic '.$this->config['basic'];
        } else {
            $headers[] = 'Authorization: Bearer '.$this->config['token'];
        }

        if (!$this->config['upload']) {
            $headers[] = 'Content-Type: application/json';
        } else {
            $headers[] = 'Content-Type: multipart/form-data';
        }
        return $headers;
    }

    /**
     * Função responsável por realizar a autenticação com a API
     *
     * @access public
     * @return array
     */
    public function auth() :array
    {
        $this->setAuthenticating(true);

        try {
            $params = [
                [
                    'name' => 'grant_type',
                    'value' => 'client_credentials'
                ],
                [
                    'name' => 'client_id',
                    'value' => $this->getClientId()
                ],
                [
                    'name' => 'client_secret',
                    'value' => $this->getClientSecret()
                ]
            ];

            $dados = $this->post("token", [], $params);

            if ($dados['httpCode'] >= 200 && $dados['httpCode'] <= 299) {
                return $dados;
            }

            if (isset($dados['body']->message)) {
                throw new Exception($dados['body']->message, 1);
            }

            if (isset($dados['body']->error_description)) {
                throw new Exception($dados['body']->error_description, 1);
            }

            throw new Exception(json_encode($dados), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        } finally {
            $this->setAuthenticating(false);
        }
    }

    /**
     * Função responsável por checar um token Hotmart
     *
     * @param string $token Token obtido pela função auth
     *
     * @access public
     * @return bool
     */
    public function checkToken(string $token) :bool
    {
        $defaultToken = $this->getToken();
        $this->setToken($token);

        try {
            $dados = $this->get("subscriptions");

            return $dados['httpCode'] != 401;
        } catch (Exception $error) {
            throw new Exception($error, 1);
        } finally {
            $this->setToken($defaultToken);
        }
    }

    /**
     * Função responsável por obter a lista de assinaturas
     *
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function listaAssinaturas(array $params = []) :array
    {
        try {
            $dados = $this->get("subscriptions", $params);

            if ($dados['httpCode'] >= 200 && $dados['httpCode'] <= 299) {
                return $dados;
            }

            if (isset($dados['body']->message)) {
                throw new Exception($dados['body']->message, 1);
            }

            if (isset($dados['body']->errors)) {
                throw new Exception(implode("\r\n", $dados['body']->errors), 1);
            }

            throw new Exception(json_encode($dados), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por obter a lista compras de um assinante
     *
     * @param string $code Código do assinante
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function listaCompras(string $code, array $params = []) :array
    {
        try {
            $dados = $this->get("subscriptions/$code/purchases", $params);

            if ($dados['httpCode'] >= 200 && $dados['httpCode'] <= 299) {
                return $dados;
            }

            if (isset($dados['body']->message)) {
                throw new Exception($dados['body']->message, 1);
            }

            if (isset($dados['body']->errors)) {
                throw new Exception(implode("\r\n", $dados['body']->errors), 1);
            }

            throw new Exception(json_encode($dados), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por cancelar uma única assinatura
     *
     * @param string $code Código do assinante
     * @param bool $send_mail Indica se deve enviar e-mail para o assinante
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function cancelaAssinatura(string $code, bool $send_mail = false, array $params = []) :array
    {
        try {
            $dados = $this->post("subscriptions/$code/cancel", [ 'send_mail' => $send_mail ], $params);

            if ($dados['httpCode'] >= 200 && $dados['httpCode'] <= 299) {
                return $dados;
            }

            if (isset($dados['body']->message)) {
                throw new Exception($dados['body']->message, 1);
            }

            if (isset($dados['body']->errors)) {
                throw new Exception(implode("\r\n", $dados['body']->errors), 1);
            }

            throw new Exception(json_encode($dados), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por cancelar várias assinaturas em massa
     *
     * @param array $codes array com os códigos dos assinantes
     * @param bool $send_mail Indica se deve enviar e-mail para cada assinante
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function cancelaMultiplasAssinaturas(array $codes, bool $send_mail = false, array $params = []) :array
    {
        try {
            $dados = [
                'subscriber_code' => $codes,
                'send_mail' => $send_mail
            ];

            $dados = $this->post("subscriptions/cancel", $dados, $params);

            if ($dados['httpCode'] >= 200 && $dados['httpCode'] <= 299) {
                return $dados;
            }

            if (isset($dados['body']->message)) {
                throw new Exception($dados['body']->message, 1);
            }

            if (isset($dados['body']->errors)) {
                throw new Exception(implode("\r\n", $dados['body']->errors), 1);
            }

            throw new Exception(json_encode($dados), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por reativar uma única assinatura
     *
     * @param string $code Código do assinante
     * @param bool $charge Indica se deve gerar uma nova cobrança
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function reativaAssinatura(string $code, bool $charge = false, array $params = []) :array
    {
        try {
            $dados = $this->post("subscriptions/$code/reactivate", [ 'charge' => $charge ], $params);

            if ($dados['httpCode'] >= 200 && $dados['httpCode'] <= 299) {
                return $dados;
            }

            if (isset($dados['body']->message)) {
                throw new Exception($dados['body']->message, 1);
            }

            if (isset($dados['body']->errors)) {
                throw new Exception(implode("\r\n", $dados['body']->errors), 1);
            }

            throw new Exception(json_encode($dados), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por cancelar várias assinaturas em massa
     *
     * @param array $codes array com os códigos dos assinantes
     * @param bool $charge Indica se deve gerar uma nova cobrança para as assinaturas
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function reativaMultiplasAssinaturas(array $codes, bool $charge = false, array $params = []) :array
    {
        try {
            $dados = [
                'subscriber_code' => $codes,
                'charge' => $charge
            ];

            $dados = $this->post("subscriptions/reactivate", $dados, $params);

            if ($dados['httpCode'] >= 200 && $dados['httpCode'] <= 299) {
                return $dados;
            }

            if (isset($dados['body']->message)) {
                throw new Exception($dados['body']->message, 1);
            }

            if (isset($dados['body']->errors)) {
                throw new Exception(implode("\r\n", $dados['body']->errors), 1);
            }

            throw new Exception(json_encode($dados), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por alterar o dia de vencimento de uma assinatura
     *
     * @param array $code Código do assinante
     * @param int $due_day Dia do vencimento
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function alteraVencimentoAssinatura(string $code, int $due_day, array $params = []) :array
    {
        if ($due_day < 1 || $due_day > 31) {
            throw new Exception("Dia de vencimento inválido!", 1);
        }

        try {
            $dados = [
                'due_day' => (int)$due_day
            ];

            $dados = $this->patch("subscriptions/$code", $dados, $params);

            if ($dados['httpCode'] >= 200 && $dados['httpCode'] <= 299) {
                return $dados;
            }

            if (isset($dados['body']->message)) {
                throw new Exception($dados['body']->message, 1);
            }

            if (isset($dados['body']->errors)) {
                throw new Exception(implode("\r\n", $dados['body']->errors), 1);
            }

            throw new Exception(json_encode($dados), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }
    
    /**
     * Função responsável por obter a lista de users
     *
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function listaUsers(array $params = []) :array
    {
        try {
            // $dados = $this->get("subscriptions", $params);

            $dados = $this->get("sales/users", $params);

            if ($dados['httpCode'] >= 200 && $dados['httpCode'] <= 299) {
                return $dados;
            }

            if (isset($dados['body']->message)) {
                throw new Exception($dados['body']->message, 1);
            }

            if (isset($dados['body']->errors)) {
                throw new Exception(implode("\r\n", $dados['body']->errors), 1);
            }

            throw new Exception(json_encode($dados), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Execute a GET Request
     *
     * @param string $path
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     *
     * @access protected
     * @return array
     */
    protected function get(string $path, array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_HTTPHEADER => $this->getDefaultHeaders()
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], $headers);
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a POST Request
     *
     * @param string $path
     * @param string $body
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     *
     * @access protected
     * @return array
     */
    protected function post(string $path, array $body = [], array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => !$this->config['upload'] ? json_encode($body) : $this->convertToFormData($body),
            CURLOPT_HTTPHEADER => $this->getDefaultHeaders()
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], $headers);
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a PUT Request
     *
     * @param string $path
     * @param string $body
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     *
     * @access protected
     * @return array
     */
    protected function put(string $path, array $body = [], array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_HTTPHEADER => $this->getDefaultHeaders(),
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => json_encode($body)
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], $headers);
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a PATCH Request
     *
     * @param string $path
     * @param string $body
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     *
     * @access protected
     * @return array
     */
    protected function patch(string $path, array $body = [], array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_HTTPHEADER => $this->getDefaultHeaders(),
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS => json_encode($body)
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], $headers);
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a DELETE Request
     *
     * @param string $path
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     *
     * @access protected
     * @return array
     */
    protected function delete(string $path, array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_HTTPHEADER => $this->getDefaultHeaders(),
            CURLOPT_CUSTOMREQUEST => "DELETE"
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], $headers);
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a OPTION Request
     *
     * @param string $path
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     *
     * @access protected
     * @return array
     */
    protected function options(string $path, array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_CUSTOMREQUEST => "OPTIONS"
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = $headers;
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Função responsável por realizar a requisição e devolver os dados
     *
     * @param string $path Rota a ser acessada
     * @param array $opts Opções do CURL
     * @param array $params Parametros query a serem passados para requisição
     *
     * @access protected
     * @return array
     */
    protected function execute(string $path, array $opts = [], array $params = []) :array
    {
        if (!preg_match("/^\//", $path)) {
            $path = '/' . $path;
        }

        if ($this->config['authenticating']) {
            $url = self::$AUTH_URL;
        } else {
            $url = self::$API_URL[$this->config['environment']];
        }

        $url .= $path;

        $curlC = curl_init();

        if (!empty($opts)) {
            curl_setopt_array($curlC, $opts);
        }

        if (!empty($params)) {
            $paramsJoined = [];

            foreach ($params as $param) {
                if (isset($param['name']) && !empty($param['name']) && isset($param['value']) && (!empty($param['value']) || $param['value'] == 0)) {
                    $paramsJoined[] = urlencode($param['name'])."=".urlencode($param['value']);
                }
            }

            if (!empty($paramsJoined)) {
                $params = '?'.implode('&', $paramsJoined);
                $url = $url.$params;
            }
        }

        curl_setopt($curlC, CURLOPT_URL, $url);
        curl_setopt($curlC, CURLOPT_RETURNTRANSFER, true);
        if (!empty($dados)) {
            curl_setopt($curlC, CURLOPT_POSTFIELDS, json_encode($dados));
        }
        $retorno = curl_exec($curlC);
        $info = curl_getinfo($curlC);
        $return["body"] = ($this->config['decode'] || !$this->config['decode'] && $info['http_code'] != '200') ? json_decode($retorno) : $retorno;
        $return["httpCode"] = curl_getinfo($curlC, CURLINFO_HTTP_CODE);
        if ($this->config['debug']) {
            $return['info'] = curl_getinfo($curlC);
        }
        curl_close($curlC);

        return $return;
    }

    /**
     * Função responsável por montar o corpo de uma requisição no formato aceito pelo FormData
     */
    private function convertToFormData($data)
    {
        $dados = [];

        $recursive = false;
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $dados[$key] = $value;
            } else {
                foreach ($value as $subkey => $subvalue) {
                    $dados[$key.'['.$subkey.']'] = $subvalue;

                    if (is_array($subvalue)) {
                        $recursive = true;
                    }
                }
            }
        }

        if ($recursive) {
            return $this->convertToFormData($dados);
        }

        return $dados;
    }
}
