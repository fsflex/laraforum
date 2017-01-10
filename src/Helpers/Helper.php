<?php
namespace FsFlex\LaraForum\Helpers;

use FsFlex\LaraForum\Models\Country;
use FsFlex\LaraForum\Models\Reach;
use FsFlex\LaraForum\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use FsFlex\LaraForum\Models\Channel;
use Illuminate\Support\Facades\DB;

class Helper
{
    public static function isAdmin($user)
    {

        if (!$user)
            return false;
        if (is_string($user))
            return $user === config('laraforum.admin_username');
        if (is_object($user))
            return $user->name === config('laraforum.admin_username');
        return false;
    }

    public static function getDiscussTemplateRequire(&$user = -1, &$channels = -1)
    {
        if ($user !== -1)
            $user = (Auth::check()) ? User::with('profile')->find(Auth::user()->id) : null;
        if ($channels !== -1)
            $channels = Helper::getChannels();
        return 1;
    }

    /**
     * @return $channels same as Channel::all() in cache;
     */
    public static function getChannels()
    {
        if (Cache::has('channels'))
            return Cache::get('channels');
        $channels = Channel::orderBy('name')->get();
        Cache::forever('channels', $channels);
        return $channels;
    }

    /**
     *  use after update table channel;
     */
    public static function forgetChannels()
    {
        if (Cache::has('channels'))
            Cache::forget('channels');
        return true;
    }

    /**
     * @return $countries same as Country::all() in cache;
     */
    public static function getCountries()
    {
        if (Cache::has('countries'))
            return Cache::get('countries');
        $basic_country = Country::where('short_name', 'us')->first();
        if(!$basic_country)
        {
            Helper::loadCountriesTable();
            $basic_country = Country::where('short_name', 'us')->first();
        }
        $countries_data = Country::where('short_name', '<>', 'us')->orderBy('name')->get();
        $countries = [];
        $countries[] = $basic_country;
        foreach ($countries_data as $country) {
            $countries[] = $country;
        }
        $countries = collect($countries);
        Cache::forever('countries', $countries);
        return $countries;
    }

    /**
     * use if update table Countries
     */
    public static function forgetCountries()
    {
        if (Cache::has('countries'))
            Cache::forget('countries');
        return true;
    }
    public static function loadReachesTable()
    {
        $data = [
            [
                'name' => 'notify',
                'description' => 'Want an email each time this thread receives a new reply?'
            ],
            [
                'name' => 'favourite',
                'description' => 'Want to favorite this conversation?'
            ],
            [
                'name' => 'dislike',
                'description' => 'Is this conversation full of spam? Sheesh - people, right?'
            ],
        ];
        DB::table('reaches')->insert($data);
    }
    public static function loadCountriesTable()
    {
        $raw_data = [["us","United States"],["af","Afghanistan"],["al","Albania"],["dz","Algeria"],["as","American Samoa"],
            ["ad","Angola"],["ai","Anguilla"],["aq","Antarctica"],["ag","Antigua and Barbuda"],["ar","Argentina"],
            ["am","Armenia"],["aw","Aruba"],["au","Australia"],["at","Austria"],["az","Azerbaijan"],["bs","Bahamas"],
            ["bh","Bahrain"],["bd","Bangladesh"],["bb","Barbados"],["by","Belarus"],["be","Belgium"],["bz","Belize"],
            ["bj","Benin"],["bm","Bermuda"],["bt","Bhutan"],["bo","Bolivia"],["ba","Bosnia and Herzegowina"],
            ["bw","Botswana"],["bv","Bouvet Island"],["br","Brazil"],["io","British Indian Ocean Territory"],
            ["bn","Brunei Darussalam"],["bg","Bulgaria"],["bf","Burkina Faso"],["bi","Burundi"],["kh","Cambodia"],
            ["cm","Cameroon"],["ca","Canada"],["cv","Cabo Verde"],["ky","Cayman Islands"],["cf","Central African Republic"],
            ["td","Chad"],["cl","Chile"],["cn","China"],["cx","Christmas Island"],["cc","Cocos (Keeling) Islands"],
            ["co","Colombia"],["km","Comoros"],["cg","Congo"],["cd","Congo, the Democratic Republic of the"],
            ["ck","Cook Islands"],["cr","Costa Rica"],["ci","Cote d'Ivoire"],["hr","Croatia (Hrvatska)"],["cu","Cuba"],
            ["cy","Cyprus"],["cz","Czech Republic"],["dk","Denmark"],["dj","Djibouti"],["dm","Dominica"],
            ["do","Dominican Republic"],["tl","East Timor"],["ec","Ecuador"],["eg","Egypt"],["sv","El Salvador"],
            ["gq","Equatorial Guinea"],["er","Eritrea"],["ee","Estonia"],["et","Ethiopia"],
            ["fk","Falkland Islands (Malvinas)"],["fo","Faroe Islands"],["fj","Fiji"],["fi","Finland"],
            ["fr","France"],["gf","French Guiana"],["pf","French Polynesia"],["tf","French Southern Territories"],
            ["ga","Gabon"],["gm","Gambia"],["ge","Georgia"],["de","Germany"],["gh","Ghana"],["gi","Gibraltar"],
            ["gr","Greece"],["gl","Greenland"],["gd","Grenada"],["gp","Guadeloupe"],["gu","Guam"],["gt","Guatemala"],
            ["gn","Guinea"],["gw","Guinea-Bissau"],["gy","Guyana"],["ht","Haiti"],["hm","Heard and Mc Donald Islands"],
            ["va","Holy See (Vatican City State)"],["hn","Honduras"],["hk","Hong Kong"],["hu","Hungary"],["is","Iceland"],
            ["in","India"],["id","Indonesia"],["ir","Iran (Islamic Republic of)"],["iq","Iraq"],["ie","Ireland"],
            ["il","Israel"],["it","Italy"],["jm","Jamaica"],["jp","Japan"],["jo","Jordan"],["kz","Kazakhstan"],
            ["ke","Kenya"],["ki","Kiribati"],["kp","Korea, Democratic People's Republic of"],["kr","Korea, Republic of"],
            ["kw","Kuwait"],["kg","Kyrgyzstan"],["la","Lao, People's Democratic Republic"],["lv","Latvia"],["lb","Lebanon"],
            ["ls","Lesotho"],["lr","Liberia"],["ly","Libyan Arab Jamahiriya"],["li","Liechtenstein"],["lt","Lithuania"],
            ["lu","Luxembourg"],["mo","Macao"],["mk","Macedonia, The Former Yugoslav Republic of"],["mg","Madagascar"],
            ["mw","Malawi"],["my","Malaysia"],["mv","Maldives"],["ml","Mali"],["mt","Malta"],["mh","Marshall Islands"],
            ["mq","Martinique"],["mr","Mauritania"],["mu","Mauritius"],["yt","Mayotte"],["mx","Mexico"],
            ["fm","Micronesia, Federated States of"],["md","Moldova, Republic of"],["mc","Monaco"],["mn","Mongolia"],
            ["ms","Montserrat"],["ma","Morocco"],["mz","Mozambique"],["mm","Myanmar"],["na","Namibia"],["nr","Nauru"],
            ["np","Nepal"],["nl","Netherlands"],["an","Netherlands Antilles"],["nc","New Caledonia"],["nz","New Zealand"],
            ["ni","Nicaragua"],["ne","Niger"],["ng","Nigeria"],["nu","Niue"],["nf","Norfolk Island"],
            ["mp","Northern Mariana Islands"],["no","Norway"],["om","Oman"],["pk","Pakistan"],["pw","Palau"],["pa","Panama"],
            ["pg","Papua New Guinea"],["py","Paraguay"],["pe","Peru"],["ph","Philippines"],["pn","Pitcairn"],["pl","Poland"],
            ["pt","Portugal"],["pr","Puerto Rico"],["qa","Qatar"],["re","Reunion"],["ro","Romania"],
            ["ru","Russian Federation"],["rw","Rwanda"],["kn","Saint Kitts and Nevis"],["lc","Saint Lucia"],
            ["vc","Saint Vincent and the Grenadines"],["ws","Samoa"],["sm","San Marino"],["st","Sao Tome and Principe"],
            ["sa","Saudi Arabia"],["sn","Senegal"],["sc","Seychelles"],["sl","Sierra Leone"],["sg","Singapore"],
            ["sk","Slovakia (Slovak Republic)"],["si","Slovenia"],["sb","Solomon Islands"],["so","Somalia"],
            ["za","South Africa"],["gs","South Georgia and the South Sandwich Islands"],["es","Spain"],["lk","Sri Lanka"],
            ["sh","St. Helena"],["pm","St. Pierre and Miquelon"],["sd","Sudan"],["sr","Suriname"],
            ["sj","Svalbard and Jan Mayen Islands"],["sz","Swaziland"],["se","Sweden"],["ch","Switzerland"],
            ["sy","Syrian Arab Republic"],["tw","Taiwan"],["tj","Tajikistan"],["tz","Tanzania, United Republic of"],
            ["th","Thailand"],["tg","Togo"],["tk","Tokelau"],["to","Tonga"],["tt","Trinidad and Tobago"],["tn","Tunisia"],
            ["tr","Turkey"],["tm","Turkmenistan"],["tc","Turks and Caicos Islands"],["tv","Tuvalu"],["ug","Uganda"],
            ["ua","Ukraine"],["ae","United Arab Emirates"],["gb","United Kingdom"],
            ["um","United States Minor Outlying Islands"],["uy","Uruguay"],["uz","Uzbekistan"],["vu","Vanuatu"],
            ["ve","Venezuela"],["vn","Vietnam"],["vg","Virgin Islands (British)"],["vi","Virgin Islands (U.S.)"],
            ["wf","Wallis and Futuna Islands"],["eh","Western Sahara"],["ye","Yemen"],["yu","Serbia"],["zm","Zambia"],
            ["zw","Zimbabwe"]];
        $data =[];
        foreach($raw_data as $row)
        {
            $data[]=[
                "short_name"=>$row[0],
                "name"=>$row[1]
            ];
        }
        DB::table('countries')->insert($data);
    }
    public static function getReaches()
    {
        if (Cache::has('reaches'))
            return Cache::get('reaches');
        $reaches = Reach::all();
        Cache::forever('reaches', $reaches);
        return $reaches;
    }

    public static function forgetReaches()
    {
        if (Cache::has('reaches'))
            Cache::forget('reaches');
        return true;
    }

    public static function getColorsLibrary()
    {
        if (Cache::has('colors_library'))
            return Cache::get('colors_library');
        $data = [
            ['name' => 'aliceblue', 'hexcode' => '#F0F8FF'], ['name' => 'antiquewhite', 'hexcode' => '#FAEBD7'], ['name' => 'aqua', 'hexcode' => '#00FFFF'],
            ['name' => 'aquamarine', 'hexcode' => '#7FFFD4'], ['name' => 'azure', 'hexcode' => '#F0FFFF'], ['name' => 'beige', 'hexcode' => '#F5F5DC'],
            ['name' => 'bisque', 'hexcode' => '#FFE4C4'], ['name' => 'blanchedalmond', 'hexcode' => '#FFEBCD'], ['name' => 'blueviolet', 'hexcode' => '#8A2BE2'],
            ['name' => 'burlywood', 'hexcode' => '#DEB887'], ['name' => 'cadetblue', 'hexcode' => '#5F9EA0'], ['name' => 'chartreuse', 'hexcode' => '#7FFF00'],
            ['name' => 'chocolate', 'hexcode' => '#D2691E'], ['name' => 'coral', 'hexcode' => '#FF7F50'], ['name' => 'cornflower', 'hexcode' => '#6495ED'],
            ['name' => 'cornsilk', 'hexcode' => '#FFF8DC'], ['name' => 'crimson', 'hexcode' => '#DC143C'], ['name' => 'cyan', 'hexcode' => '#00FFFF'],
            ['name' => 'darkcyan', 'hexcode' => '#008B8B'], ['name' => 'darkgoldenrod', 'hexcode' => '#B8860B'], ['name' => 'darkgray', 'hexcode' => '#A9A9A9'],
            ['name' => 'darkkhaki', 'hexcode' => '#BDB76B'], ['name' => 'darkorange', 'hexcode' => '#FF8C00'], ['name' => 'darkorchid', 'hexcode' => '#9932CC'],
            ['name' => 'darksalmon', 'hexcode' => '#E9967A'], ['name' => 'darkseagreen', 'hexcode' => '#8FBC8B'], ['name' => 'darkturquoise', 'hexcode' => '#00CED1'],
            ['name' => 'darkviolet', 'hexcode' => '#9400D3'], ['name' => 'deeppink', 'hexcode' => '#FF1493'], ['name' => 'deepskyblue', 'hexcode' => '#00BFFF'],
            ['name' => 'dimgray', 'hexcode' => '#696969'], ['name' => 'dodgerblue', 'hexcode' => '#1E90FF'], ['name' => 'floralwhite', 'hexcode' => '#FFFAF0'],
            ['name' => 'forestgreen', 'hexcode' => '#228B22'], ['name' => 'fuchsia', 'hexcode' => '#FF00FF'], ['name' => 'gainsboro', 'hexcode' => '#DCDCDC'],
            ['name' => 'ghostwhite', 'hexcode' => '#F8F8FF'], ['name' => 'gold', 'hexcode' => '#FFD700'], ['name' => 'goldenrod', 'hexcode' => '#DAA520'],
            ['name' => 'gray', 'hexcode' => '#808080'], ['name' => 'green', 'hexcode' => '#008000'], ['name' => 'greenyellow', 'hexcode' => '#ADFF2F'],
            ['name' => 'honeydew', 'hexcode' => '#F0FFF0'], ['name' => 'hotpink', 'hexcode' => '#FF69B4'], ['name' => 'indianred', 'hexcode' => '#CD5C5C'],
            ['name' => 'ivory', 'hexcode' => '#FFFFF0'], ['name' => 'khaki', 'hexcode' => '#F0E68C'], ['name' => 'lavender', 'hexcode' => '#E6E6FA'],
            ['name' => 'lavenderblush', 'hexcode' => '#FFF0F5'], ['name' => 'lawngreen', 'hexcode' => '#7CFC00'], ['name' => 'lemonchiffon', 'hexcode' => '#FFFACD'],
            ['name' => 'lightblue', 'hexcode' => '#ADD8E6'], ['name' => 'lightcoral', 'hexcode' => '#F08080'], ['name' => 'lightcyan', 'hexcode' => '#E0FFFF'],
            ['name' => 'lightgreen', 'hexcode' => '#90EE90'], ['name' => 'lightgray', 'hexcode' => '#D3D3D3'], ['name' => 'lightpink', 'hexcode' => '#FFB6C1'],
            ['name' => 'lightsalmon', 'hexcode' => '#FFA07A'], ['name' => 'lightseagreen', 'hexcode' => '#20B2AA'], ['name' => 'lightskyblue', 'hexcode' => '#87CEFA'],
            ['name' => 'lightslategray', 'hexcode' => '#778899'], ['name' => 'lightsteelblue', 'hexcode' => '#B0C4DE'], ['name' => 'lightyellow', 'hexcode' => '#FFFFE0'],
            ['name' => 'lime', 'hexcode' => '#00FF00'], ['name' => 'limegreen', 'hexcode' => '#32CD32'], ['name' => 'linen', 'hexcode' => '#FAF0E6'],
            ['name' => 'magenta', 'hexcode' => '#FF00FF'], ['name' => 'mediumaquamarine', 'hexcode' => '#66CDAA'], ['name' => 'mediumorchid', 'hexcode' => '#BA55D3'],
            ['name' => 'mediumpurple', 'hexcode' => '#9370DB'], ['name' => 'mediumseagreen', 'hexcode' => '#3CB371'], ['name' => 'mediumslateblue', 'hexcode' => '#7B68EE'],
            ['name' => 'mediumspringgreen', 'hexcode' => '#00FA9A'], ['name' => 'mediumturquoise', 'hexcode' => '#48D1CC'], ['name' => 'mediumvioletred', 'hexcode' => '#C71585'],
            ['name' => 'mintcream', 'hexcode' => '#F5FFFA'], ['name' => 'mistyrose', 'hexcode' => '#FFE4E1'], ['name' => 'moccasin', 'hexcode' => '#FFE4B5'],
            ['name' => 'navajowhite', 'hexcode' => '#FFDEAD'], ['name' => 'oldlace', 'hexcode' => '#FDF5E6'], ['name' => 'olive', 'hexcode' => '#808000'],
            ['name' => 'olivedrab', 'hexcode' => '#6B8E23'], ['name' => 'orange', 'hexcode' => '#FFA500'], ['name' => 'orangered', 'hexcode' => '#FF4500'],
            ['name' => 'orchid', 'hexcode' => '#DA70D6'], ['name' => 'palegoldenrod', 'hexcode' => '#EEE8AA'], ['name' => 'palegreen', 'hexcode' => '#98FB98'],
            ['name' => 'paleturquoise', 'hexcode' => '#AFEEEE'], ['name' => 'palevioletred', 'hexcode' => '#DB7093'], ['name' => 'papayawhip', 'hexcode' => '#FFEFD5'],
            ['name' => 'peachpuff', 'hexcode' => '#FFDAB9'], ['name' => 'peru', 'hexcode' => '#CD853F'], ['name' => 'pink', 'hexcode' => '#FFC0CB'],
            ['name' => 'plum', 'hexcode' => '#DDA0DD'], ['name' => 'powderblue', 'hexcode' => '#B0E0E6'], ['name' => 'red', 'hexcode' => '#FF0000'],
            ['name' => 'rosybrown', 'hexcode' => '#BC8F8F'], ['name' => 'royalblue', 'hexcode' => '#4169E1'], ['name' => 'salmon', 'hexcode' => '#FA8072'],
            ['name' => 'sandybrown', 'hexcode' => '#F4A460'], ['name' => 'seagreen', 'hexcode' => '#2E8B57'], ['name' => 'seashell', 'hexcode' => '#FFF5EE'],
            ['name' => 'sienna', 'hexcode' => '#A0522D'], ['name' => 'silver', 'hexcode' => '#C0C0C0'], ['name' => 'skyblue', 'hexcode' => '#87CEEB'],
            ['name' => 'slateblue', 'hexcode' => '#6A5ACD'], ['name' => 'slategray', 'hexcode' => '#708090'], ['name' => 'snow', 'hexcode' => '#FFFAFA'],
            ['name' => 'springgreen', 'hexcode' => '#00FF7F'], ['name' => 'steelblue', 'hexcode' => '#4682B4'], ['name' => 'tan', 'hexcode' => '#D2B48C'],
            ['name' => 'teal', 'hexcode' => '#008080'], ['name' => 'thistle', 'hexcode' => '#D8BFD8'], ['name' => 'tomato', 'hexcode' => '#FF6347'],
            ['name' => 'turquoise', 'hexcode' => '#40E0D0'], ['name' => 'violet', 'hexcode' => '#EE82EE'], ['name' => 'wheat', 'hexcode' => '#F5DEB3'],
            ['name' => 'white', 'hexcode' => '#FFFFFF'], ['name' => 'whitesmoke', 'hexcode' => '#F5F5F5'], ['name' => 'yellow', 'hexcode' => '#FFFF00'],
            ['name' => 'yellowgreen', 'hexcode' => '#9ACD32']
        ];
        $data1 = [];
        foreach ($data as $cell) {
            $val1 = ($weight1 = hexdec(substr($cell['hexcode'], 0, 2)) < 80) ? 1 : 0;
            $val2 = ($weight2 = hexdec(substr($cell['hexcode'], 2, 2)) < 80) ? 2 : 0;
            $val3 = ($weight3 = hexdec(substr($cell['hexcode'], 4, 2)) < 80) ? 2 : 0;
            $is_weighing = ($val1 + $val2 + $val3 < 3 || $weight1 + $weight2 + $weight3 > 400) ? 0 : 1;
            $data1[] = ['name' => $cell['name'], 'hexcode' => $cell['hexcode'], 'is_weighing' => $is_weighing];
        }
        $colors = collect($data1);
        Cache::forever('colors_library', $colors);
        return $colors;
    }
}