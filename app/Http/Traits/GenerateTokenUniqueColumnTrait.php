<?php

namespace App\Http\Traits;

trait GenerateTokenUniqueColumnTrait
{

    public function getToken(
        array $model, 
        $column_unique, 
        $length_token = 5, 
        $prefix = null, 
        $type = 'all')
    {
        $fix_token = '';
        $lock = 0;
        $same = true;
        $data_token = $model;
        // check this token on model is available or not
        if (count($data_token) > 0) {
            $loop = count($data_token); // 5
            while ($same) {
                // check type is all or number\
                if ($type == 'all') {
                    $token = $this->generateToken($length_token);
                } else {
                    $token = $this->generateTokenNumber($length_token);
                }

                // check token is available or not               
                foreach ($data_token as $tok) { //5x
                    if ($tok[$column_unique] != $token) {
                        $lock++;
                    } else {
                        $lock = 0;
                    }
                }
                if ($loop == $lock) {
                    $same = false;
                    $fix_token = $token;
                }
            }

            if($prefix != null){
                $fix_token = $prefix.$fix_token;
            }

            return $fix_token;

        } else {
            // check type is all or number\
            if ($type == 'all') {
                $fix_token = $this->generateToken($length_token);
            } else {
                $fix_token = $this->generateTokenNumber($length_token);
            }
            
            if($prefix != null){
                $fix_token = $prefix.$fix_token;
            }

            return $fix_token;
        }
    }

    // Not used yet
    // public function getTokenMoreEficientTemp()
    // {
    //     $token = $this->generateToken($length_token);
    //     $model = $model[0];
    //     $column = $model::where($column_unique, $token)->first();
    //     if ($column) {
    //         $this->getToken($model, $column_unique, $length_token);
    //     } else {
    //         return $token;
    //     }
    // }

    public function generateToken($length = 32)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function generateTokenNumber($length = 32)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
