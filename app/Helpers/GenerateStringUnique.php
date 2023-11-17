<?php

namespace App\Helpers;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;

class GenerateStringUnique
{
    private $capitalOnly = false; // default false
    protected $column_unique; // column name
    protected $model; // model name

    /**
     * 
     * @param array $model | model data list in array
     * @param string $column_unique | column name that want to be unique
     * @return void 
     */
    public function __construct(array $model, string $column_unique)
    {
        $this->model = $model;
        $this->column_unique = $column_unique;
    }

    private function availableType(): array
    {
        return [
            'all',
            'splited',
            'number'
        ];
    }

    public function needCapitalOnly()
    {
        $this->capitalOnly = true;
        return $this;
    }

    private function getTokenTypeBased($lengthToken, $type): string
    {
        $token = '';
        if ($type == 'all') {
            $token = $this->generateToken($lengthToken);
        } elseif ($type == 'splited') {
            $token = $this->generateSplitedToken($lengthToken);
        } else {
            $token = $this->generateTokenNumber($lengthToken);
        }

        return $token;
    }

    /**
     * 
     * @param int $length_token 
     * @param string|null $prefix 
     * @param string $type 
     * @return string 
     * @throws Exception 
     */
    public function getToken($length_token = 6, string $prefix = null, $type = 'all'): string
    {
        // private variable
        $listedData = $this->model;
        $column_unique = $this->column_unique;
        $same = true;
        $lock = 0;
        $fixedToken = '';

        if (!in_array($type, $this->availableType())) {
            throw new \Exception("Type is not available");
        }

        if (count($listedData) > 0) {
            $loop = count($listedData);
            while ($same) {
                $token = $this->getTokenTypeBased($length_token, $type);
                
                // check token is available or not               
                foreach ($listedData as $tok) { //5x
                    if ($tok[$column_unique] != $token) {
                        $lock++;
                    } else {
                        $lock = 0;
                    }
                }
                if ($loop == $lock) {
                    $same = false;
                    $fixedToken = $token;
                }
            }

            if($prefix != null){
                if ($type != 'splited')
                    $fixedToken = $prefix.$fixedToken;
            }

            return $fixedToken;

        } else {
            $fixedToken = $this->getTokenTypeBased($length_token, $type);
            
            if($prefix != null){
                if ($type != 'splited')
                $fixedToken = $prefix.$fixedToken;
            }

            return $fixedToken;
        }
    }

    private function generateToken($length = 32, $capitalTextOnly = false)
    {
        $capital = $capitalTextOnly ? $capitalTextOnly : $this->capitalOnly;

        if ($capital) {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        }

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    private function generateTokenNumber($length = 32)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    private function generateSplitedToken($length = 32, $split = 4, $delimiter = '-', $capitalTextOnly = true)
    {
        $token = $this->generateToken($length, $capitalTextOnly);
        $splited_token = str_split($token, $split);
        $token = implode($delimiter, $splited_token);

        return $token;
    }

    /**
     * 
     * @param string $string 
     * @return string 
     */
    private function random_username(string $string): string
    {
        $pattern = " ";
        $firstPart = strstr(strtolower($string), $pattern, true);
        $secondPart = substr(strstr(strtolower($string), $pattern, false), 0,3);
        $nrRand = rand(0, 100);
        
        $username = trim($firstPart).trim($secondPart).trim($nrRand);
        return $username;
    }

    /**
     * 
     * @param string $full_name | full name of user
     * @return string 
     */
    public function username(string $full_name): string
    {
        $model = $this->model;
        $unique_column = $this->column_unique;
        $fix_username = '';
        $lock = 0;
        $same = true;
        $data_username = $model;
        // check this token on model is available or not
        if (count($data_username) > 0) {
            $loop = count($data_username); // 5
            while ($same) {
                $username = $this->random_username($full_name);
                // check token is available or not               
                foreach ($data_username as $user) { //5x
                    if ($user[$unique_column] != $username) {
                        $lock++;
                    } else {
                        $lock = 0;
                    }
                }
                if ($loop == $lock) {
                    $same = false;
                    $fix_username = $username;
                }
            }

            return $fix_username;

        } else {
            $fix_username = $this->random_username($full_name);
            return $fix_username;
        }
    }

    /**
     * 
     * @param array $model | model data list in array
     * @param string $column_unique | column name that want to be unique
     * @return static 
     */
    public static function make(array $model, string $column_unique)
    {
        return new static($model, $column_unique);
    }


}