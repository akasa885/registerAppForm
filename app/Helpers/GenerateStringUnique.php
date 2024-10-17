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
     * @param array $modelName | model name
     * @param string $column_unique | column name that want to be unique
     */
    public function __construct(string $modelName, string $column_unique)
    {
        $modelClass = 'App\Models\\'.$modelName;
        if (!class_exists($modelClass)) {
            throw new \Exception('Model not found');
        }

        $this->model = $modelClass;
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

    /**
     * Make token
     *
     * @param string $type
     * @param integer $length_token
     * @return string
     */
    private function makeToken(string $type, $length_token): string
    {
        if ($type == 'all') {
            return $this->generateToken($length_token);
        } else if ($type == 'number') {
            return $this->generateTokenNumber($length_token);
        } else if ($type == 'splited') {
            return $this->generateSplitedToken($length_token);
        }
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
        $modelClass = $this->model;
        $column_unique = $this->column_unique;
        $fixToken = null;

        if (!in_array($type, $this->availableType())) {
            throw new \Exception("Type is not available");
        }

        $fixToken = $this->makeToken($type, $length_token);
        $exists = true;

        while ($exists) {
            if ($prefix != null) { $fixToken = $prefix . $fixToken; }
            $column = $modelClass::select($column_unique)->where($column_unique, $fixToken)->first();

            if ($column) {
                $fixToken = $this->makeToken($type, $length_token);
            } else {
                $exists = false;
            }
        }

        return $fixToken;
    }

    private function generateToken($length = 32, $capitalTextOnly = false)
    {
        $capital = !$this->capitalOnly ? $capitalTextOnly : $this->capitalOnly;

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
        $data_username = $model::select($unique_column)->get()->toArray();
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
     * @param string $modelName | model name
     * @param string $column_unique | column name that want to be unique
     * @return GenerateStringUnique 
     */
    public static function make(string $modelName, string $column_unique)
    {
        return new static($modelName, $column_unique);
    }


}