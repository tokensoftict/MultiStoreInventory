<?php


use App\Models\Task;
use App\Models\Warehousestore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Classes\Settings;
use Spatie\Valuestore\Valuestore;

function generateRandom($length = 25) {
    $characters = 'abcdefghijklmnopqrstuvwxyz_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function loadUserMenu($group_id = NULL)
{
    $group_id = $group_id === NULL ? auth()->user()->group_id : $group_id;

    return Cache::remember('route-permission-'.$group_id,86400, function() use ($group_id){
        return \App\Models\Group::with(['tasks'=>function ($q) {
            $q->join('modules', 'modules.id', '=', 'tasks.module_id');
            $q->orderBy('tasks.module_id', "ASC")->orderBy('tasks.id');
        },'permissions','users','tasks','group_tasks','tasks.module'])->find($group_id)->tasks;
    });
}

function myAccessGroup()
{
    return request()->user()->group;
}

function getUserMenu()
{

    $groupMenu = loadUserMenu();

    $task = Task::whereRoute(Route::currentRouteName())->first();
    $currentModule = NULL;
    if($task){
        $currentModule = $task->module_id;
    }

    $userMenus = '<li '.(Route::currentRouteName() === 'dashboard' ? 'class="active"' : "").'><a href="' . route("dashboard") . '">Dashboard</a></li>';

    if ($groupMenu) {
        $lastModule = '';
        $isFirstRun = true;

        foreach ($groupMenu as $menu) {
            if(in_array($menu->module_id, Settings::$reports)) continue;
            $taskIcon = !empty($menu->icon) ? $menu->icon : "fa fa-arrow-right";
            if ($lastModule != $menu->module_id) {
                if ($lastModule != '' && !$isFirstRun) {
                    $userMenus .= '</ul></li>';
                }
                $isFirstRun = false;
                $userMenus .= '<li class="dropdown '.($currentModule == $menu->module_id ? 'active' : '').'">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-submenu="" aria-expanded="false">' . $menu->module->label . '</a>
                <ul class="dropdown-menu" style="font-size: 12.5px">';
            }
            if ($menu->visibility) $userMenus .= '<li><a href="' . route($menu->route) . '">' . $menu->name . '</a></li>';
            $lastModule = $menu->module_id;
        }
        if (!$isFirstRun) {
            $userMenus .= '</ul></li>';
        }
    }

    $userMenus .= '<li '.(Route::currentRouteName() === 'reports' ? 'class="active"' : "").'><a href="' . route("reports") . '">System Reports</a></li>';

    return $userMenus;
}

/**
 * Fetch the list of tasks the user is assigned.
 *
 * @return mixed
 */
function userPermissions()
{
    return loadUserMenu();
}

/**
 * Check whether a user has access to a particular route.
 *
 * @param $route
 * @return mixed
 */
function userCanView($route)
{
    $route = trim($route);
    return userPermissions()->contains(function ($task, $key) use ($route) {
        return $task->route == $route;
    });
}

function str_plural($name){
    return Str::plural($name);
}

/**
 * Set Controller Default Layout And Render Content
 *
 * @param string $content
 * @return \Illuminate\Http\Response
 */
function setPageContent($pageblade, $data = array(), $layout = 'layouts.app')
{
    return view($layout, ['content' => view($pageblade, $data)]);
}

function getStoreSettings(){
    return json_decode(json_encode(Valuestore::make(storage_path('app/settings.json'))->all()));
}

function month_year($time = false, $pad = false)
{
    if (!$time) $time = time() + time_offset();
    else $time = strtotime($time);
    if ($pad) $pad = ". h:i:s A";
    else $pad = "";
    return date('F, Y' . $pad, $time);
}

function time_offset()
{
    return 0;
}

function str_date($time = false, $pad = false)
{
    if (!$time) $time = time() + time_offset();
    else $time = strtotime($time);
    if ($pad) $pad = ". h:i:s A";
    else $pad = "";
    return date('l, F jS, Y' . $pad, $time);
}

function str_date2($time = false, $pad = false)
{
    if (!$time) $time = time() + time_offset();
    else $time = strtotime($time);
    if ($pad) $pad = ". h:i:s A";
    else $pad = "";
    return date('D, F jS, Y' . $pad, $time);
}

function format_date($date, $withTime = TRUE)
{
    if ($date == "0000-00-00 00:00:00") {
        return "Never";
    }

    $date = trim($date);
    $retVal = "";
    $date_time_array = explode(" ", $date);
    $time = $date_time_array[1];
    $time_array = explode(":", $time);

    $date_array = explode("-", "$date");
    $day = $date_array['2'];
    $month = $date_array['1'];
    $year = $date_array['0'];
    if ($year > 0) {
        @ $ddate = mktime(12, 12, 12, $month, $day, $year);
        @ $retVal = date("j M Y", $ddate);
    }

    if (!empty($time)) {
        $hr = $time_array[0];
        $min = $time_array[1];
        $sec = $time_array[2];
        @ $ddate = mktime($hr, $min, $sec, $month, $day, $year);
        @ $retVal = date("j M Y, H:i", $ddate);
        if (!$withTime) {
            @ $retVal = date("j M Y", $ddate);
        }
    }

    return $retVal;
}

function restructureDate($date_string)
{
    if (strtotime($date_string)) return $date_string;

    if (str_contains($date_string, "/")) {
        if (strtotime(str_replace("/", "-", $date_string))) return str_replace("/", "-", $date_string);

        // TODO: try to change the date format to make it easier for the system to parse
    }

    return $date_string;
}

function render($type = "append")
{
    echo "@render:$type=out>>";
}

function json_success($data)
{
    return json_status(true, $data, 'success');
}

function json_failure($data, $code_name)
{
    return json_status(false, $data, $code_name);
}

function response_array_failure($data, $code_name)
{
    return response_array_status(false, $data, $code_name);
}

function json_status($status, $data, $code_name)
{

    $response = response_array_status($status, $data, $code_name);

    return json($response);
}

function response_array_status($status, $data, $code_name)
{
    if (!$statuses = config("statuses." . $code_name)) $statuses = config("statuses.unknown");

    $response = [
        'status' => $status,
        'status_code' => $statuses[0],
        'message' => $statuses[1],
    ];

    if ($status) {
        if (is_bool($data)) $data = ($data) ? "true" : "false";

        $response['data'] = $data;
        $response['validation'] = null;
        return $response;
    }

    $response['data'] = null;
    $response['validation'] = $data;
    return $response;
}

function json($response)
{
    return response()->json($response);
}

function normal_case($str)
{
    return ucwords(str_replace("_", " ", Str::snake(str_replace("App\\", "", $str))));
}

function getPaginate()
{
    return Request::get('paginate');
}

function status($status){
    if(is_array($status))
       return  \App\Models\Status::whereIn('name',$status)->pluck('id');
    else
        return \App\Models\Status::where('name',$status)->first()->id;
}

function showStatus($status)
{
    $st = \App\Models\Status::where('name',$status)->first();
    if(!$st) return label($status);
    return label($st->name,$st->label);
}

function label($text, $type = 'default', $extra = 'sm')
{
    return '<span class="label label-' . $type . '" label-form>' . $text . '</span>';
}

function alert_success($msg)
{
    return alert('success', $msg);
}

function alert_info($msg)
{
    return alert('info', $msg);
}

function alert_warning($msg)
{
    return alert('warning', $msg);
}

function alert_error($msg)
{
    return alert('danger', $msg);
}

function alert($status, $msg)
{
    return '<div class="alert alert-' . $status . '">' . $msg . '</div>';
}

function money($amt)
{
    return number_format($amt, 2);
}

/**
 * Return a capitalised string
 *
 * @return string
 * @param string $string
 */
function toCap($string)
{
    return strtoupper(strtolower($string));
}

/**
 * Return a small letter string
 *
 * @return string
 * @param string $string
 */
function toSmall($string)
{
    return strtolower($string);
}

/**
 * Return a sentence case string
 *
 * @return string
 * @param string $string
 */
function toSentence($string)
{
    return ucwords(strtolower($string));
}

function generateRandomString($randStringLength)
{
    $timestring = microtime();
    $secondsSinceEpoch = (integer)substr($timestring, strrpos($timestring, " "), 100);
    $microseconds = (double)$timestring;
    $seed = mt_rand(0, 1000000000) + 10000000 * $microseconds + $secondsSinceEpoch;
    mt_srand($seed);
    $randstring = "";
    for ($i = 0; $i < $randStringLength; $i++) {
        $randstring .= mt_rand(0, 9);
    }
    return ($randstring);
}


/**
 * Get IDs of the Work Groups this User has been granted permission to work on.
 * @return array
 */



function getRandomString_AlphaNum($length)
{
    //Init the pool of characters by category
    $pool[0] = "ABCDEFGHJKLMNPQRSTUVWXYZ";
    $pool[1] = "23456789";
    return randomString_Generator($length, $pool);
}   //END getRandomString_AlphaNum()


function randomString_Num($length)
{
    //Init the pool of characters by category
    $pool[0] = "0123456789";
    return randomString_Generator($length, $pool);
}

function getRandomString_AlphaNumSigns($length)
{
    //Init the pool of characters by category
    $pool[0] = "ABCDEFGHJKLMNPQRSTUVWXYZ";
    $pool[1] = "abcdefghjkmnpqrstuvwxyz";
    $pool[2] = "23456789";
    $pool[3] = "-_";
    return randomString_Generator($length, $pool);
}

function randomString_Generator($length, $pools)
{
    $highest_pool_index = count($pools) - 1;
    //Now generate the string
    $finalResult = "";
    $length = abs((int)$length);
    for ($counter = 0; $counter < $length; $counter++) {
        $whichPool = rand(0, $highest_pool_index);    //Randomly select the pool to use
        $maxPos = strlen($pools[$whichPool]) - 1;    //Get the max number of characters in the pool to be used
        $finalResult .= $pools[$whichPool][mt_rand(0, $maxPos)];
    }
    return $finalResult;
}

/**
 * The only difference between this and date is that it works with the env time offet
 * @param $format
 * @param $signed_seconds
 * @return bool|string
 */
if (!function_exists("now")) {
    function now($format = 'Y-m-d H:i:s', $signed_seconds = 0)
    {
        return date($format, ((time() + (env('TIME_OFFSET_HOURS', 0) * 60)) + $signed_seconds));
    }
}

function removeSpecialCharacter($string)
{
    // return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    return preg_replace('/\'/', '', $string);
}

function getPaperAttributes($paperSize)
{
    $paperSize = strtolower($paperSize);
    switch ($paperSize){
        case "a4l":
            $size = "A4";
            $orientation = "Landscape";
            $startX = 785;
            $startY = 570;
            $font = 9;
            break;
        case "a4p":
            $size = "A4";
            $orientation = "Portrait";
            $startX = 540;
            $startY = 820;
            $font = 9;
            break;
        case "a3l":
            $size = "A3";
            $orientation = "Landscape";
            $startX = 1130;
            $startY = 820;
            $font = 9;
            break;
        case "a3p":
            $size = "A3";
            $orientation = "Portrait";
            $startX = 785;
            $startY = 1165;
            $font = 9;
            break;
        case "us-sff":
            $size = "U.S. Standard Fanfold";
            $orientation = "Landscape";
            $startX = 692;
            $startY = 585;
            $font = 9;
            break;
        default:
            $size = "A4";
            $orientation = "Landscape";
            $startX = 785;
            $startY = 570;
            $font = 9;
            break;
    }
    return [$size, $orientation, $startX, $startY, $font];
}

function softwareStampWithDate($width = "100px") {
    return "<br>
    Generated @". date('Y-m-d H:i A') ;
}

function string_to_secret(string $string = NULL)
{
    if (!$string) return NULL;

    $length = strlen($string);
    $visibleCount = (int) round($length / 4);
    $hiddenCount = $length - ($visibleCount * 2);

    return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
}



function split_name($name)
{
    $name = trim($name);
    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $first_name = trim(preg_replace('#' . $last_name . '#', '', $name));
    return array("firstname" => $first_name, "lastname" => $last_name);
}

function convert_date($date){
    return date('D, F jS, Y', strtotime($date));
}

function convert_date_with_time($date){
    return date('D, F jS, Y h:i a', strtotime($date));
}

function convert_date2($date){
    return date('Y/m/d', strtotime($date));
}


function invoice_status($status){
    if($status == "DRAFT"){
        return label($status,"info");
    }else if($status == "PAID"){
        return label(ucwords($status),"success");
    }else if($status == "DISCOUNT"){
        return label(strtoupper("Waiting for Discount"),"primary");
    }else if($status == "VOID"){
        return label(ucwords($status),"warning");
    }else if($status == "HOLD"){
        return label(ucwords($status),"default");
    }else if($status == "COMPLETE"){
        return label(ucwords($status),"success");
    }else if($status == "DELETED"){
        return label(ucwords($status),"danger");
    }
}


function dailySales(){
   return \App\Models\Invoice::where('invoice_date',dailyDate())->where('status','COMPLETE')->sum('total_amount_paid');
}


function weeklySales(){
    return \App\Models\Invoice::whereBetween('invoice_date',weeklyDateRange())->where('status','COMPLETE')->sum('total_amount_paid');
}

function monthlySales(){
    return \App\Models\Invoice::whereBetween('invoice_date',monthlyDateRange())->where('status','COMPLETE')->sum('total_amount_paid');
}

function dailyDate(){
    return date('Y-m-d');
}

function weeklyDateRange(){
    $dt = strtotime (date('Y-m-d'));
    $range =  array (
        date ('N', $dt) == 1 ? date ('Y-m-d', $dt) : date ('Y-m-d', strtotime ('last monday', $dt)),
        date('N', $dt) == 7 ? date ('Y-m-d', $dt) : date ('Y-m-d', strtotime ('next sunday', $dt))
    );

    return $range;
}

function monthlyDateRange(){
    $dt = strtotime (date('Y-m-d'));
    $range =  array (
        date ('Y-m-d', strtotime ('first day of this month', $dt)),
        date ('Y-m-d', strtotime ('last day of this month', $dt))
    );
    return $range;
}


function getActualStore($product_type,$store_selected){

    $store = Warehousestore::find($store_selected);

    if($product_type == "NORMAL") {
       return $store->packed_column;
    }
    if($product_type == "PACKED") {
        return $store->yard_column;
    }

    return $store->packed_column;

}


function getActiveStore($force = false){

    if(session()->has('activeStore')) {
        return (object)session()->get('activeStore');
    }

    return Warehousestore::select('id','name','packed_column','yard_column')->where('default',1)->where('status',1)->first();
}

function getStockActualCostPrice($stock,$product_type){
    if(is_numeric($stock)) $stock = \App\Models\Stock::findorfail($stock);

    if($product_type == "NORMAL")  return $stock->cost_price;

    if($product_type == "PACKED")  return $stock->yard_cost_price;

    return $stock->cost_price;

}

function getStockActualSellingPrice($stock,$product_type){
    if(is_numeric($stock)) $stock = \App\Models\Stock::findorfail($stock);

    if($product_type == "NORMAL")  return $stock->selling_price;

    if($product_type == "PACKED")  return $stock->yard_selling_price;

    return $stock->selling_price;

}


function loadPDF($link){
    return  '<iframe src ="'.asset('/laraview/#../pdf/'.$link."?v=".mt_rand()).'" style="width: 100%; margin-top: 20px" height="600px"></iframe>';
}
