<?php
    function msg($success,$status,$message,$extra = []){
        return array_merge([
            'success' => $success,
            'status' => $status,
            'message' => $message
        ],$extra);
    }
    
    function polygonArea($X, $Y, $n) { 
        // Initialze area 
        $area = 0.0; 
    
        // Calculate value of 
        // shoelace formula 
        $j = $n - 1; 
        for ($i = 0; $i < $n; $i++) 
        { 
            $area += ($X[$j] + $X[$i]) * ($Y[$j] - $Y[$i]); 
                    
            // j is previous vertex to i          
            $j = $i;  
        } 
    
        // Return absolute value 
        return abs($area / 2.0); 
    } 