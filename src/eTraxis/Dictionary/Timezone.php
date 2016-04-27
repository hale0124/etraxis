<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Dictionary;

use Dictionary\StaticDictionary;

/**
 * Timezones.
 */
class Timezone extends StaticDictionary
{
    const FALLBACK = 0;

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            0   => 'UTC',
            1   => 'Africa/Abidjan',
            2   => 'Africa/Accra',
            3   => 'Africa/Addis Ababa',
            4   => 'Africa/Algiers',
            5   => 'Africa/Asmara',
            6   => 'Africa/Bamako',
            7   => 'Africa/Bangui',
            8   => 'Africa/Banjul',
            9   => 'Africa/Bissau',
            10  => 'Africa/Blantyre',
            11  => 'Africa/Brazzaville',
            12  => 'Africa/Bujumbura',
            13  => 'Africa/Cairo',
            14  => 'Africa/Casablanca',
            15  => 'Africa/Ceuta',
            16  => 'Africa/Conakry',
            17  => 'Africa/Dakar',
            18  => 'Africa/Dar es Salaam',
            19  => 'Africa/Djibouti',
            20  => 'Africa/Douala',
            21  => 'Africa/El Aaiun',
            22  => 'Africa/Freetown',
            23  => 'Africa/Gaborone',
            24  => 'Africa/Harare',
            25  => 'Africa/Johannesburg',
            26  => 'Africa/Juba',
            27  => 'Africa/Kampala',
            28  => 'Africa/Khartoum',
            29  => 'Africa/Kigali',
            30  => 'Africa/Kinshasa',
            31  => 'Africa/Lagos',
            32  => 'Africa/Libreville',
            33  => 'Africa/Lome',
            34  => 'Africa/Luanda',
            35  => 'Africa/Lubumbashi',
            36  => 'Africa/Lusaka',
            37  => 'Africa/Malabo',
            38  => 'Africa/Maputo',
            39  => 'Africa/Maseru',
            40  => 'Africa/Mbabane',
            41  => 'Africa/Mogadishu',
            42  => 'Africa/Monrovia',
            43  => 'Africa/Nairobi',
            44  => 'Africa/Ndjamena',
            45  => 'Africa/Niamey',
            46  => 'Africa/Nouakchott',
            47  => 'Africa/Ouagadougou',
            48  => 'Africa/Porto-Novo',
            49  => 'Africa/Sao Tome',
            50  => 'Africa/Tripoli',
            51  => 'Africa/Tunis',
            52  => 'Africa/Windhoek',
            53  => 'America/Adak',
            54  => 'America/Anchorage',
            55  => 'America/Anguilla',
            56  => 'America/Antigua',
            57  => 'America/Araguaina',
            58  => 'America/Argentina/Buenos Aires',
            59  => 'America/Argentina/Catamarca',
            60  => 'America/Argentina/Cordoba',
            61  => 'America/Argentina/Jujuy',
            62  => 'America/Argentina/La Rioja',
            63  => 'America/Argentina/Mendoza',
            64  => 'America/Argentina/Rio Gallegos',
            65  => 'America/Argentina/Salta',
            66  => 'America/Argentina/San Juan',
            67  => 'America/Argentina/San Luis',
            68  => 'America/Argentina/Tucuman',
            69  => 'America/Argentina/Ushuaia',
            70  => 'America/Aruba',
            71  => 'America/Asuncion',
            72  => 'America/Atikokan',
            73  => 'America/Bahia',
            74  => 'America/Bahia Banderas',
            75  => 'America/Barbados',
            76  => 'America/Belem',
            77  => 'America/Belize',
            78  => 'America/Blanc-Sablon',
            79  => 'America/Boa Vista',
            80  => 'America/Bogota',
            81  => 'America/Boise',
            82  => 'America/Cambridge Bay',
            83  => 'America/Campo Grande',
            84  => 'America/Cancun',
            85  => 'America/Caracas',
            86  => 'America/Cayenne',
            87  => 'America/Cayman',
            88  => 'America/Chicago',
            89  => 'America/Chihuahua',
            90  => 'America/Costa Rica',
            91  => 'America/Cuiaba',
            92  => 'America/Curacao',
            93  => 'America/Danmarkshavn',
            94  => 'America/Dawson',
            95  => 'America/Dawson Creek',
            96  => 'America/Denver',
            97  => 'America/Detroit',
            98  => 'America/Dominica',
            99  => 'America/Edmonton',
            100 => 'America/Eirunepe',
            101 => 'America/El Salvador',
            102 => 'America/Fortaleza',
            103 => 'America/Glace Bay',
            104 => 'America/Godthab',
            105 => 'America/Goose Bay',
            106 => 'America/Grand Turk',
            107 => 'America/Grenada',
            108 => 'America/Guadeloupe',
            109 => 'America/Guatemala',
            110 => 'America/Guayaquil',
            111 => 'America/Guyana',
            112 => 'America/Halifax',
            113 => 'America/Havana',
            114 => 'America/Hermosillo',
            115 => 'America/Indiana/Indianapolis',
            116 => 'America/Indiana/Knox',
            117 => 'America/Indiana/Marengo',
            118 => 'America/Indiana/Petersburg',
            119 => 'America/Indiana/Tell City',
            120 => 'America/Indiana/Vevay',
            121 => 'America/Indiana/Vincennes',
            122 => 'America/Indiana/Winamac',
            123 => 'America/Inuvik',
            124 => 'America/Iqaluit',
            125 => 'America/Jamaica',
            126 => 'America/Juneau',
            127 => 'America/Kentucky/Louisville',
            128 => 'America/Kentucky/Monticello',
            129 => 'America/Kralendijk',
            130 => 'America/La Paz',
            131 => 'America/Lima',
            132 => 'America/Los Angeles',
            133 => 'America/Lower Princes',
            134 => 'America/Maceio',
            135 => 'America/Managua',
            136 => 'America/Manaus',
            137 => 'America/Marigot',
            138 => 'America/Martinique',
            139 => 'America/Matamoros',
            140 => 'America/Mazatlan',
            141 => 'America/Menominee',
            142 => 'America/Merida',
            143 => 'America/Metlakatla',
            144 => 'America/Mexico City',
            145 => 'America/Miquelon',
            146 => 'America/Moncton',
            147 => 'America/Monterrey',
            148 => 'America/Montevideo',
            149 => 'America/Montreal',
            150 => 'America/Montserrat',
            151 => 'America/Nassau',
            152 => 'America/New York',
            153 => 'America/Nipigon',
            154 => 'America/Nome',
            155 => 'America/Noronha',
            156 => 'America/North Dakota/Beulah',
            157 => 'America/North Dakota/Center',
            158 => 'America/North Dakota/New Salem',
            159 => 'America/Ojinaga',
            160 => 'America/Panama',
            161 => 'America/Pangnirtung',
            162 => 'America/Paramaribo',
            163 => 'America/Phoenix',
            164 => 'America/Port-au-Prince',
            165 => 'America/Port of Spain',
            166 => 'America/Porto Velho',
            167 => 'America/Puerto Rico',
            168 => 'America/Rainy River',
            169 => 'America/Rankin Inlet',
            170 => 'America/Recife',
            171 => 'America/Regina',
            172 => 'America/Resolute',
            173 => 'America/Rio Branco',
            174 => 'America/Santa Isabel',
            175 => 'America/Santarem',
            176 => 'America/Santiago',
            177 => 'America/Santo Domingo',
            178 => 'America/Sao Paulo',
            179 => 'America/Scoresbysund',
            180 => 'America/Shiprock',
            181 => 'America/Sitka',
            182 => 'America/St Barthelemy',
            183 => 'America/St Johns',
            184 => 'America/St Kitts',
            185 => 'America/St Lucia',
            186 => 'America/St Thomas',
            187 => 'America/St Vincent',
            188 => 'America/Swift Current',
            189 => 'America/Tegucigalpa',
            190 => 'America/Thule',
            191 => 'America/Thunder Bay',
            192 => 'America/Tijuana',
            193 => 'America/Toronto',
            194 => 'America/Tortola',
            195 => 'America/Vancouver',
            196 => 'America/Whitehorse',
            197 => 'America/Winnipeg',
            198 => 'America/Yakutat',
            199 => 'America/Yellowknife',
            200 => 'Antarctica/Casey',
            201 => 'Antarctica/Davis',
            202 => 'Antarctica/DumontDUrville',
            203 => 'Antarctica/Macquarie',
            204 => 'Antarctica/Mawson',
            205 => 'Antarctica/McMurdo',
            206 => 'Antarctica/Palmer',
            207 => 'Antarctica/Rothera',
            208 => 'Antarctica/South Pole',
            209 => 'Antarctica/Syowa',
            210 => 'Antarctica/Vostok',
            211 => 'Arctic/Longyearbyen',
            212 => 'Asia/Aden',
            213 => 'Asia/Almaty',
            214 => 'Asia/Amman',
            215 => 'Asia/Anadyr',
            216 => 'Asia/Aqtau',
            217 => 'Asia/Aqtobe',
            218 => 'Asia/Ashgabat',
            219 => 'Asia/Baghdad',
            220 => 'Asia/Bahrain',
            221 => 'Asia/Baku',
            222 => 'Asia/Bangkok',
            223 => 'Asia/Beirut',
            224 => 'Asia/Bishkek',
            225 => 'Asia/Brunei',
            226 => 'Asia/Choibalsan',
            227 => 'Asia/Chongqing',
            228 => 'Asia/Colombo',
            229 => 'Asia/Damascus',
            230 => 'Asia/Dhaka',
            231 => 'Asia/Dili',
            232 => 'Asia/Dubai',
            233 => 'Asia/Dushanbe',
            234 => 'Asia/Gaza',
            235 => 'Asia/Harbin',
            236 => 'Asia/Ho Chi Minh',
            237 => 'Asia/Hong Kong',
            238 => 'Asia/Hovd',
            239 => 'Asia/Irkutsk',
            240 => 'Asia/Jakarta',
            241 => 'Asia/Jayapura',
            242 => 'Asia/Jerusalem',
            243 => 'Asia/Kabul',
            244 => 'Asia/Kamchatka',
            245 => 'Asia/Karachi',
            246 => 'Asia/Kashgar',
            247 => 'Asia/Kathmandu',
            248 => 'Asia/Kolkata',
            249 => 'Asia/Krasnoyarsk',
            250 => 'Asia/Kuala Lumpur',
            251 => 'Asia/Kuching',
            252 => 'Asia/Kuwait',
            253 => 'Asia/Macau',
            254 => 'Asia/Magadan',
            255 => 'Asia/Makassar',
            256 => 'Asia/Manila',
            257 => 'Asia/Muscat',
            258 => 'Asia/Nicosia',
            259 => 'Asia/Novokuznetsk',
            260 => 'Asia/Novosibirsk',
            261 => 'Asia/Omsk',
            262 => 'Asia/Oral',
            263 => 'Asia/Phnom Penh',
            264 => 'Asia/Pontianak',
            265 => 'Asia/Pyongyang',
            266 => 'Asia/Qatar',
            267 => 'Asia/Qyzylorda',
            268 => 'Asia/Rangoon',
            269 => 'Asia/Riyadh',
            270 => 'Asia/Sakhalin',
            271 => 'Asia/Samarkand',
            272 => 'Asia/Seoul',
            273 => 'Asia/Shanghai',
            274 => 'Asia/Singapore',
            275 => 'Asia/Taipei',
            276 => 'Asia/Tashkent',
            277 => 'Asia/Tbilisi',
            278 => 'Asia/Tehran',
            279 => 'Asia/Thimphu',
            280 => 'Asia/Tokyo',
            281 => 'Asia/Ulaanbaatar',
            282 => 'Asia/Urumqi',
            283 => 'Asia/Vientiane',
            284 => 'Asia/Vladivostok',
            285 => 'Asia/Yakutsk',
            286 => 'Asia/Yekaterinburg',
            287 => 'Asia/Yerevan',
            288 => 'Atlantic/Azores',
            289 => 'Atlantic/Bermuda',
            290 => 'Atlantic/Canary',
            291 => 'Atlantic/Cape Verde',
            292 => 'Atlantic/Faroe',
            293 => 'Atlantic/Madeira',
            294 => 'Atlantic/Reykjavik',
            295 => 'Atlantic/South Georgia',
            296 => 'Atlantic/St Helena',
            297 => 'Atlantic/Stanley',
            298 => 'Australia/Adelaide',
            299 => 'Australia/Brisbane',
            300 => 'Australia/Broken Hill',
            301 => 'Australia/Currie',
            302 => 'Australia/Darwin',
            303 => 'Australia/Eucla',
            304 => 'Australia/Hobart',
            305 => 'Australia/Lindeman',
            306 => 'Australia/Lord Howe',
            307 => 'Australia/Melbourne',
            308 => 'Australia/Perth',
            309 => 'Australia/Sydney',
            310 => 'Europe/Amsterdam',
            311 => 'Europe/Andorra',
            312 => 'Europe/Athens',
            313 => 'Europe/Belgrade',
            314 => 'Europe/Berlin',
            315 => 'Europe/Bratislava',
            316 => 'Europe/Brussels',
            317 => 'Europe/Bucharest',
            318 => 'Europe/Budapest',
            319 => 'Europe/Chisinau',
            320 => 'Europe/Copenhagen',
            321 => 'Europe/Dublin',
            322 => 'Europe/Gibraltar',
            323 => 'Europe/Guernsey',
            324 => 'Europe/Helsinki',
            325 => 'Europe/Isle of Man',
            326 => 'Europe/Istanbul',
            327 => 'Europe/Jersey',
            328 => 'Europe/Kaliningrad',
            329 => 'Europe/Kiev',
            330 => 'Europe/Lisbon',
            331 => 'Europe/Ljubljana',
            332 => 'Europe/London',
            333 => 'Europe/Luxembourg',
            334 => 'Europe/Madrid',
            335 => 'Europe/Malta',
            336 => 'Europe/Mariehamn',
            337 => 'Europe/Minsk',
            338 => 'Europe/Monaco',
            339 => 'Europe/Moscow',
            340 => 'Europe/Oslo',
            341 => 'Europe/Paris',
            342 => 'Europe/Podgorica',
            343 => 'Europe/Prague',
            344 => 'Europe/Riga',
            345 => 'Europe/Rome',
            346 => 'Europe/Samara',
            347 => 'Europe/San Marino',
            348 => 'Europe/Sarajevo',
            349 => 'Europe/Simferopol',
            350 => 'Europe/Skopje',
            351 => 'Europe/Sofia',
            352 => 'Europe/Stockholm',
            353 => 'Europe/Tallinn',
            354 => 'Europe/Tirane',
            355 => 'Europe/Uzhgorod',
            356 => 'Europe/Vaduz',
            357 => 'Europe/Vatican',
            358 => 'Europe/Vienna',
            359 => 'Europe/Vilnius',
            360 => 'Europe/Volgograd',
            361 => 'Europe/Warsaw',
            362 => 'Europe/Zagreb',
            363 => 'Europe/Zaporozhye',
            364 => 'Europe/Zurich',
            365 => 'Indian/Antananarivo',
            366 => 'Indian/Chagos',
            367 => 'Indian/Christmas',
            368 => 'Indian/Cocos',
            369 => 'Indian/Comoro',
            370 => 'Indian/Kerguelen',
            371 => 'Indian/Mahe',
            372 => 'Indian/Maldives',
            373 => 'Indian/Mauritius',
            374 => 'Indian/Mayotte',
            375 => 'Indian/Reunion',
            376 => 'Pacific/Apia',
            377 => 'Pacific/Auckland',
            378 => 'Pacific/Chatham',
            379 => 'Pacific/Chuuk',
            380 => 'Pacific/Easter',
            381 => 'Pacific/Efate',
            382 => 'Pacific/Enderbury',
            383 => 'Pacific/Fakaofo',
            384 => 'Pacific/Fiji',
            385 => 'Pacific/Funafuti',
            386 => 'Pacific/Galapagos',
            387 => 'Pacific/Gambier',
            388 => 'Pacific/Guadalcanal',
            389 => 'Pacific/Guam',
            390 => 'Pacific/Honolulu',
            391 => 'Pacific/Johnston',
            392 => 'Pacific/Kiritimati',
            393 => 'Pacific/Kosrae',
            394 => 'Pacific/Kwajalein',
            395 => 'Pacific/Majuro',
            396 => 'Pacific/Marquesas',
            397 => 'Pacific/Midway',
            398 => 'Pacific/Nauru',
            399 => 'Pacific/Niue',
            400 => 'Pacific/Norfolk',
            401 => 'Pacific/Noumea',
            402 => 'Pacific/Pago Pago',
            403 => 'Pacific/Palau',
            404 => 'Pacific/Pitcairn',
            405 => 'Pacific/Pohnpei',
            406 => 'Pacific/Port Moresby',
            407 => 'Pacific/Rarotonga',
            408 => 'Pacific/Saipan',
            409 => 'Pacific/Tahiti',
            410 => 'Pacific/Tarawa',
            411 => 'Pacific/Tongatapu',
            412 => 'Pacific/Wake',
            413 => 'Pacific/Wallis',
        ];
    }
}
