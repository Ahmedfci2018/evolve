<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use DB;

class ManageController extends Controller
{
    public function products_get_data()
    {
        $dbhost = Config::get('database.connections.mysql.host');
        $dbuser = Config::get('database.connections.mysql.username');
        $dbpass = Config::get('database.connections.mysql.password');
        $dbname = Config::get('database.connections.mysql.database');
        $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
        $backupAlert = '';
        $tables = array();
        $result = mysqli_query($connection, "SHOW TABLES");
        if (!$result) {
            $backupAlert = 'Error found.<br/>ERROR : ' . mysqli_error($connection) . 'ERROR NO :' . mysqli_errno($connection);
        } else {
            while ($row = mysqli_fetch_row($result)) {
                $tables[] = $row[0];
            }
            mysqli_free_result($result);

            $return = '';
            foreach ($tables as $table) {

                $result = mysqli_query($connection, "SELECT * FROM " . $table);
                if (!$result) {
                    $backupAlert = 'Error found.<br/>ERROR : ' . mysqli_error($connection) . 'ERROR NO :' . mysqli_errno($connection);
                } else {
                    $num_fields = mysqli_num_fields($result);
                    if (!$num_fields) {
                        $backupAlert = 'Error found.<br/>ERROR : ' . mysqli_error($connection) . 'ERROR NO :' . mysqli_errno($connection);
                    } else {
                        $return .= 'DROP TABLE ' . $table . ';';
                        $row2 = mysqli_fetch_row(mysqli_query($connection, 'SHOW CREATE TABLE ' . $table));
                        if (!$row2) {
                            $backupAlert = 'Error found.<br/>ERROR : ' . mysqli_error($connection) . 'ERROR NO :' . mysqli_errno($connection);
                        } else {
                            $return .= "\n\n" . $row2[1] . ";\n\n";
                            for ($i = 0; $i < $num_fields; $i++) {
                                while ($row = mysqli_fetch_row($result)) {
                                    $return .= 'INSERT INTO ' . $table . ' VALUES(';
                                    for ($j = 0; $j < $num_fields; $j++) {
                                        $row[$j] = addslashes($row[$j]);
                                        if (isset($row[$j])) {
                                            $return .= '"' . $row[$j] . '"';
                                        } else {
                                            $return .= '""';
                                        }
                                        if ($j < $num_fields - 1) {
                                            $return .= ',';
                                        }
                                    }
                                    $return .= ");\n";
                                }
                            }
                            $return .= "\n\n\n";
                        }

                        // $backup_file = $dbname . date("Y-m-d-H-i-s") . '.sql';
                        // $handle = fopen("{$backup_file}", 'w+');
                        // fwrite($handle, $return);
                        // fclose($handle);
                        // $backupAlert = 'Succesfully got the backup!';
                    }
                }
            }
            $backup_file = $dbname . date("Y-m-d-H-i-s") . '.sql';
                        $handle = fopen("{$backup_file}", 'w+');
                        fwrite($handle, $return);
                        fclose($handle);
                        $backupAlert = 'Succesfully got the backup!';
                        
        }
        // echo $backupAlert;

                $images = glob(public_path('images/'));
                \Madzipper::make(public_path('images.zip'))->add($images)->close();
                $image_zip = glob(public_path('images.zip'));
                $seeds_zip = glob(public_path($backup_file));
                \Madzipper::make(public_path('backup.zip'))->add($images)->add($seeds_zip)->close();
                unlink(public_path('images.zip'));
                // unlink(public_path($backup_file));
                return response()->download(public_path('backup.zip'));

        // $seeds_zip = glob(public_path($backup_file));
        // \Madzipper::make(public_path('backup.zip'))->add($seeds_zip)->close();
        // // unlink(public_path('images.zip'));
        // unlink(public_path($backup_file));
        // return response()->download(public_path('backup.zip'));
            


        //         $tables = array();
        //         $result = DB::select("SHOW TABLES");
        //         $var = 'Tables_in_' . Config::get('database.connections.mysql.database');
        //         foreach ($result as $results) {
        // //             SELECT *
        // // FROM INFORMATION_SCHEMA.COLUMNS
        // // WHERE TABLE_SCHEMA = 'test'
        // // AND TABLE_NAME = 'products';
        //             $tables[] = $results->$var;
        //         }
        //         $return = '';
            

        //         //$table ='users';
        //         foreach ($tables as $table) {
        //             $return .= 'TRUNCATE ' . $table . '; ';

        //             $result = DB::table($table)->get();
        //             foreach ($result as $key => $value) {
        //                 $return_fields = '';
        //                 $return_values = '';

        //                 $return_fields .= 'INSERT INTO ' . $table . ' (';
        //                 $return_values .= ' VALUES (';
        //                 $array = (array) $value;
        //                 $i = 0;

        //                 foreach ($array as $key => $value) {
        //                     $value = addslashes($value);
        //                     if ($i == 0) {
        //                         $return_values .= "'" . $value . "'";
        //                         $return_fields .= "`" . $key . "`";
        //                     } else {
        //                         $return_values .= ", '" . $value . "'";
        //                         $return_fields .= ", `" . $key . "`";
        //                     }

        //                     $i++;
        //                 }
        //                 $return_values .= ");";
        //                 $return_fields .= ");";
        //                 $return .= $return_fields . $return_values . "\n\n\n";
        //             }

        //         }
        //         $handle = fopen('backup.sql', 'w+');
        //         fwrite($handle, $return);
        //         fclose($handle);
        //         $images = glob(public_path('images/'));
        //         \Madzipper::make(public_path('images.zip'))->add($images)->close();
        //         $image_zip = glob(public_path('images.zip'));
        //         $seeds_zip = glob(public_path('backup.sql'));
        //         \Madzipper::make(public_path('backup.zip'))->add($image_zip)->add($seeds_zip)->close();
        //         unlink(public_path('images.zip'));
        //         unlink(public_path('backup.sql'));
        //         return response()->download(public_path('backup.zip'));


        # code...
    }

    public function products_delete_data()
    {
        $database_name=Config::get('database.connections.mysql.database');
        DB::statement("DROP DATABASE `{$database_name}`");
        unlink(public_path('backup.zip'));
        // DB::statement("DROP DATABASE `{$database_name}`");
        // shell_exec("mysqldump -u root -p drugly > drugly2.sql");
        
    }
}


