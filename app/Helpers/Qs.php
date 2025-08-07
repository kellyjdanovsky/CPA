<?php

namespace App\Helpers;

use App\Models\Setting;
use App\Models\StudentRecord;
use App\Models\Subject;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;

class Qs
{
    public static function displayError($errors)
    {
        foreach ($errors as $err) {
            $data[] = $err;
        }
        return '
                <div class="alert alert-danger alert-styled-left alert-dismissible">
									<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
									<span class="font-weight-semibold">Oops!</span> '.
        implode(' ', $data).'
							    </div>
                ';
    }

    public static function getAppCode()
    {
        return self::getSetting('system_title') ?: 'CJ';
    }

    public static function getDefaultUserImage()
    {
        return asset('global_assets/images/user.png');
    }

    public static function getPanelOptions()
    {
        return '    <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                        <a class="list-icons-item" data-action="remove"></a>
                    </div>
                </div>';
    }

    public static function displaySuccess($msg)
    {
        return '
 <div class="alert alert-success alert-bordered">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button> '.
        $msg.'  </div>
                ';
    }

    public static function getTeamSA()
    {
        return ['admin', 'super_admin'];
    }

    public static function getTeamAccount()
    {
        return ['admin', 'super_admin', 'accountant'];
    }

    public static function getTeamSAT()
    {
        return ['admin', 'super_admin', 'teacher'];
    }

    public static function getTeamAcademic()
    {
        return ['admin', 'super_admin', 'teacher', 'student'];
    }

    public static function getTeamAdministrative()
    {
        return ['admin', 'super_admin', 'accountant'];
    }

    public static function hash($id)
    {
        $date = date('dMY').'CJ';
        $hash = new Hashids($date, 14);
        return $hash->encode($id);
    }

    public static function getUserRecord($remove = [])
    {
        $data = ['name', 'phone', 'phone2', 'dob', 'gender', 'address', 'religion', 'bg_id', 'nal_id', 'state_id', 'lga_id', 'status', 'nom_p', 'prof_p', 'nom_m', 'prof_m'];

        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    public static function getStaffRecord($remove = [])
    {
        $data = ['emp_date',];

        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    public static function getStudentData($remove = [])
    {
        $data = ['my_class_id', 'section_id', 'dorm_id', 'dorm_room_no', 'year_admitted', 'house', 'age'];

        return $remove ? array_values(array_diff($data, $remove)) : $data;

    }

    public static function decodeHash($str, $toString = true)
    {
        $date = date('dMY').'CJ';
        $hash = new Hashids($date, 14);
        $decoded = $hash->decode($str);
        return $toString ? implode(',', $decoded) : $decoded;
    }

    public static function userIsTeamAccount()
    {
        return in_array(Auth::user()->user_type, self::getTeamAccount());
    }

    public static function userIsTeamSA()
    {
        return in_array(Auth::user()->user_type, self::getTeamSA());
    }

    public static function userIsTeamSAT()
    {
        return in_array(Auth::user()->user_type, self::getTeamSAT());
    }

    public static function userIsAcademic()
    {
        return in_array(Auth::user()->user_type, self::getTeamAcademic());
    }

    public static function userIsAdministrative()
    {
        return in_array(Auth::user()->user_type, self::getTeamAdministrative());
    }

    public static function userIsAdmin()
    {
        return Auth::user()->user_type == 'admin';
    }

    public static function getUserType()
    {
        return Auth::user()->user_type;
    }

    public static function userIsSuperAdmin()
    {
        return Auth::user()->user_type == 'super_admin';
    }

    public static function userIsStudent()
    {
        return Auth::user()->user_type == 'student';
    }

    public static function userIsTeacher()
    {
        return Auth::user()->user_type == 'teacher';
    }

    public static function userIsParent()
    {
        return Auth::user()->user_type == 'parent';
    }

    public static function userIsStaff()
    {
        return in_array(Auth::user()->user_type, self::getStaff());
    }

    public static function getStaff($remove=[])
    {
        $data =  ['super_admin', 'admin', 'teacher', 'accountant', 'librarian'];
        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    public static function getAllUserTypes($remove=[])
    {
        $data =  ['super_admin', 'admin', 'teacher', 'accountant', 'librarian', 'student', 'parent'];
        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    // Check if User is Head of Super Admins (Untouchable)
    public static function headSA(int $user_id)
    {
        return $user_id === 1;
    }

    public static function userIsPTA()
    {
        return in_array(Auth::user()->user_type, self::getPTA());
    }

    public static function userIsMyChild($student_id, $parent_id)
    {
        $data = ['user_id' => $student_id, 'my_parent_id' =>$parent_id];
        return StudentRecord::where($data)->exists();
    }

    public static function getSRByUserID($user_id)
    {
        return StudentRecord::where('user_id', $user_id)->first();
    }

    public static function getPTA()
    {
        return ['super_admin', 'admin', 'teacher', 'parent'];
    }

    /*public static function filesToUpload($programme)
    {
        return ['birth_cert', 'passport',  'neco_cert', 'waec_cert', 'ref1', 'ref2'];
    }*/

    public static function getPublicUploadPath()
    {
        return 'uploads/';
    }

    public static function getUserUploadPath()
    {
        return 'uploads/'.date('Y').'/'.date('m').'/'.date('d').'/';
    }

    public static function getUploadPath($user_type)
    {
        return 'uploads/'.$user_type.'/';
    }

    public static function getFileMetaData($file)
    {
        //$dataFile['name'] = $file->getClientOriginalName();
        $dataFile['ext'] = $file->getClientOriginalExtension();
        $dataFile['type'] = $file->getClientMimeType();
        $dataFile['size'] = self::formatBytes($file->getSize());
        return $dataFile;
    }

    public static function generateUserCode()
    {
        return substr(uniqid(mt_rand()), -7, 7);
    }

    public static function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }

    public static function getSetting($type)
    {
        $setting = Setting::where('type', $type)->first();

        if ($setting) {
            return $setting->description;
        }

        // Valeurs par défaut si le setting n'existe pas
        $defaults = [
            'system_name' => 'Collège Privé Adventiste Avaratetezana',
            'system_title' => 'CPA',
            'current_session' => '2024-2025',
            'system_email' => 'admin@cpa-avaratetezana.mg',
            'phone' => '+261 34 12 345 67',
            'address' => 'Avaratetezana, Antananarivo, Madagascar',
            'logo' => '/images/logo_avar.png',
            'lock_exam' => '0',
            'sch_name' => 'Collège Privé Adventiste Avaratetezana',
            'term' => 'Trimestre',
            'term_ends' => now()->format('d/m/Y'),
            'term_begins' => now()->subMonths(3)->format('d/m/Y'),
            'alt_email' => '',
            'email_host' => '',
            'email_pass' => '',
            'next_term_fees_j' => '20000',
            'next_term_fees_pn' => '25000',
            'next_term_fees_p' => '25000',
            'next_term_fees_n' => '25600',
            'next_term_fees_s' => '15600',
            'next_term_fees_c' => '1600',
        ];

        return $defaults[$type] ?? null;
    }

    public static function getCurrentSession()
    {
        // Priorité à la session sélectionnée par l'utilisateur (temporaire)
        $selectedSession = session('selected_school_year');
        if ($selectedSession) {
            return $selectedSession;
        }

        // Utiliser le setting current_session (comme dans le système existant)
        return self::getSetting('current_session');
    }

    public static function getNextSession()
    {
        $oy = self::getCurrentSession();
        $old_yr = explode('-', $oy);
        return ++$old_yr[0].'-'.++$old_yr[1];
    }

    public static function getSystemName()
    {
        return self::getSetting('system_name');
    }

    public static function findMyChildren($parent_id)
    {
        return StudentRecord::where('my_parent_id', $parent_id)->with(['user', 'my_class'])->get();
    }

    public static function findTeacherSubjects($teacher_id)
    {
        return Subject::where('teacher_id', $teacher_id)->with('my_class')->get();
    }

    public static function findStudentRecord($user_id)
    {
        return StudentRecord::where('user_id', $user_id)->first();
    }

    public static function getMarkType($class_type)
    {
       switch($class_type){
           case 'J' : return 'junior';
           case 'S' : return 'senior';
           case 'N' : return 'nursery';
           case 'P' : return 'primary';
           case 'PN' : return 'pre_nursery';
           case 'C' : return 'creche';
       }
        return $class_type;
    }

    public static function json($msg, $ok = TRUE, $arr = [])
    {
        return $arr ? response()->json($arr) : response()->json(['ok' => $ok, 'msg' => $msg]);
    }

    public static function jsonStoreOk()
    {
        return self::json(__('msg.store_ok'));
    }

    public static function jsonUpdateOk()
    {
        return self::json(__('msg.update_ok'));
    }

    public static function storeOk($routeName)
    {
        return self::goWithSuccess($routeName, __('msg.store_ok'));
    }

    public static function deleteOk($routeName)
    {
        return self::goWithSuccess($routeName, __('msg.del_ok'));
    }

    public static function updateOk($routeName)
    {
        return self::goWithSuccess($routeName, __('msg.update_ok'));
    }

    public static function goToRoute($goto, $status = 302, $headers = [], $secure = null)
    {
        $data = [];
        $to = (is_array($goto) ? $goto[0] : $goto) ?: 'dashboard';
        if(is_array($goto)){
            array_shift($goto);
            $data = $goto;
        }
        return app('redirect')->to(route($to, $data), $status, $headers, $secure);
    }

    public static function goWithDanger($to = 'dashboard', $msg = NULL)
    {
        $msg = $msg ? $msg : __('msg.rnf');
        return self::goToRoute($to)->with('flash_danger', $msg);
    }

    public static function goWithSuccess($to, $msg)
    {
        return self::goToRoute($to)->with('flash_success', $msg);
    }

    public static function getDaysOfTheWeek()
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    }

    /**
     * Calcule l'âge à partir de la date de naissance
     *
     * @param string $dob Date de naissance au format Y-m-d
     * @return int L'âge calculé
     */
    public static function calculateAge($dob)
    {
        if (!$dob) {
            return '';
        }

        try {
            // Convertir la date au format DateTime
            // Format Y-m-d (nouveau format standard)
            $birthDate = new \DateTime($dob);

            $currentDate = new \DateTime();
            $interval = $currentDate->diff($birthDate);
            return $interval->y;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une chaîne vide
            return '';
        }
    }

    /**
     * Convert number to words in French
     *
     * @param int $number
     * @return string
     */
    public static function convertToWords($number)
    {
        if ($number == 0) {
            return 'zéro';
        }

        $units = [
            '', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
            'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'
        ];

        $tens = [
            '', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'
        ];

        $hundreds = ['', 'cent', 'deux cents', 'trois cents', 'quatre cents', 'cinq cents', 'six cents', 'sept cents', 'huit cents', 'neuf cents'];

        if ($number < 20) {
            return $units[$number];
        } elseif ($number < 100) {
            $ten = intval($number / 10);
            $unit = $number % 10;

            if ($ten == 7 || $ten == 9) {
                $ten--;
                $unit += 10;
            }

            $result = $tens[$ten];
            if ($unit > 0) {
                if ($ten == 8 && $unit == 1) {
                    $result .= '-un';
                } else {
                    $result .= '-' . $units[$unit];
                }
            }
            return $result;
        } elseif ($number < 1000) {
            $hundred = intval($number / 100);
            $remainder = $number % 100;

            $result = $hundreds[$hundred];
            if ($remainder > 0) {
                $result .= ' ' . self::convertToWords($remainder);
            }
            return $result;
        } elseif ($number < 1000000) {
            $thousand = intval($number / 1000);
            $remainder = $number % 1000;

            $result = '';
            if ($thousand == 1) {
                $result = 'mille';
            } else {
                $result = self::convertToWords($thousand) . ' mille';
            }

            if ($remainder > 0) {
                $result .= ' ' . self::convertToWords($remainder);
            }
            return $result;
        } elseif ($number < 1000000000) {
            $million = intval($number / 1000000);
            $remainder = $number % 1000000;

            $result = '';
            if ($million == 1) {
                $result = 'un million';
            } else {
                $result = self::convertToWords($million) . ' millions';
            }

            if ($remainder > 0) {
                $result .= ' ' . self::convertToWords($remainder);
            }
            return $result;
        }

        return 'nombre trop grand';
    }
}
