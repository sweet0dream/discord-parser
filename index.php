<?php
    require_once 'vendor/autoload.php';
    use Stichoza\GoogleTranslate\GoogleTranslate;

    function getMessageDiscordChannel($getChannelId, $param = false) {

        if(!isset($param['limit'])) $param['limit'] = 100;
        if(!isset($param['type'])) $param['type'] = 'parse';

        if($param['type'] != 'parse') {

            $request = WpOrg\Requests\Requests::get('https://discord.com/api/v9/channels/' . $getChannelId . '/messages?limit=' . $param['limit'], [
                'authorization' => 'MTA4MDE4MzgyNjU0MzgwNDQ2Ng.GbSxYc.yToWWQ0Lzv5ACXNcutZSCAyUXWGl3yRUKFXtkA'
            ]);
            
            if($request->status_code == 200) {
                $listIdMessage = [];
                foreach(array_reverse(json_decode($request->body, true)) as $item) {
                    $listIdMessage[] = ltrim(explode("\r\n", $item['content'])[0], '#');
                }
                
                return isset($listIdMessage) && !empty($listIdMessage) ? $listIdMessage : [];
            }
        } else {

            $request = WpOrg\Requests\Requests::get('https://discord.com/api/v9/channels/' . $getChannelId . '/messages?limit=' . $param['limit'], [
                'authorization' => 'MTA4MDE4MzgyNjU0MzgwNDQ2Ng.GbSxYc.yToWWQ0Lzv5ACXNcutZSCAyUXWGl3yRUKFXtkA'
            ]);

            if($request->status_code == 200) {
                $messages = [];
                foreach(array_reverse(json_decode($request->body, true)) as $item) {
                    if(!empty($item['embeds'])) {
                        if(isset($item['embeds'][0]['url'])) {
                            $embeds = $item['embeds'][0]['url'];
                        }
                    }
                    $messages[$item['id'].'/'.$item['channel_id']] = $item['content'].(isset($embeds)&&$item['content']!=$embeds ? ' '.$embeds : '');
                    unset($embeds);
                }
            }
    
            return isset($messages) && !empty($messages) ? $messages : false;
        }
    }

    function translateMessage($message) {
        $tr = new GoogleTranslate();
        return [
            'src' => $message,
            'tsl' => $tr->setSource('en')->setTarget('ru')->translate($message)
        ];
    }

    function sendMessageDiscordChannel($sendChannelId, $message) {
        usleep(250000);
        return exec('/usr/bin/python3 /home/user/web/check.belberry.net/public_html/~other/discord-parser/send.py '.$sendChannelId.' \''.$message.'\'') == 1 ? true : false;
        usleep(250000);
    }

    $config_parse_channel = [
        //category: 🔱Traders🔱
        '📢-announcements'    =>  [909894887200145408, 1096496445638586529, 5],
        '🇨🇭-swiss-bot'        =>  [1025472935537954967, 1096496642179477595, 5],
        '📈-bitcoin-ta'       =>  [909499610530193449, 1096496694402752572, 5],
        '📉-altcoins-ta'      =>  [909499636979486750, 1096498889873440809, 5],
        '❤-testimonials'     =>  [949327110714052708, 1096498948052615278, 5],
        '📌-templates'        =>  [909407570698072095, 1096499002385649825, 5],
        //category: PRO TRADERS
        'imi'           =>  [1020288069322158180, 1096504230984040510, 5],
        'trader-r'      =>  [1020288244547592242, 1096504283903561859, 5],
        'whensailing'   =>  [1020288338520965131, 1096504334432350299, 5],
        'hpaul'         =>  [1020288367918858310, 1096504391994982400, 5],
        'dultex'        =>  [1020288396796629032, 1096504444713193633, 5],
        'dukie86'       =>  [1020288428828545034, 1096504490624032879, 5],
        'younez'        =>  [1020288550182334544, 1096504541433839739, 5],
        'pc0813'        =>  [1020288587541008414, 1096504591069220955, 5],
        'necokronos'    =>  [1020288647657955429, 1096504649823043704, 5],
        'chris00'       =>  [1020288839828385822, 1096504909228150855, 5],
        'general'       =>  [1052676548785885336, 1096504959660466206, 5],
        'pro-support'   =>  [1055296835037700166, 1096505030368043078, 5],
        'the-pro-trader'=>  [1055297700456837170, 1096505082712953042, 5],
        //category: PRO LOUNGE
        'btc-ltf-ideas'      =>  [1020284679036874864, 1096508493676155001, 5],
        'btc-htf-ideas'      =>  [1020282264732569640, 1096508543810666546, 5],
        'elliot-wave'        =>  [1020284880690622504, 1096508594041667635, 5],
        'altcoin-ideas'      =>  [1020284749325025323, 1096508646105546892, 5],
        'stocks-and-forex'   =>  [1020284836994371614, 1096508699671007282, 5],
        'pro-assistance'     =>  [1020284915419451412, 1096508750321434826, 5],
        'pro-tactics'        =>  [1025897759460569129, 1096508797746417694, 5],
        //category: video library
        'lesson-strategies'             =>  [1042500290676531220, 1096513986679283764, 5],
        'daily-screenshare-sessions'    =>  [1042500423916990546, 1096514047475724289, 5],
        'preweek-screenshare-sessions'  =>  [1042500567693537421, 1096514105331957850, 5],
        'preweek-voice-sessions'        =>  [1042500631270801409, 1096514162210910401, 5],
        'midweek-market-updates'        =>  [1042500705317027840, 1096514218477502536, 5],
        'altcoin-sessions'              =>  [1042500751504715797, 1096514271619338250, 5],
        'elliot-wave-sessions'          =>  [1042500808429813781, 1096514323737743390, 5],
        'psychology-sessions'           =>  [1042500860728594492, 1096514390297153586, 5],
        //category: coaches trading channels
        'daniel-trading-channel'    =>  [984517568071073832, 1096473552900862032, 5],
        'daniel-questions'          =>  [984518611496476803, 1096473775542898789, 5],
        'igor-trading-channel'      =>  [984517624505438248, 1096473841561260173, 5],
        'igor-questions'            =>  [984518657826762803, 1096473924025462784, 5],
        'rivalry-trading-channel'   =>  [1072193194097397800, 1096473988504490096, 5],
        'rivalry-questions'         =>  [1072658850974531634, 1096474062290698270, 5],
        'severin-trading-channel'   =>  [1072193262531653702, 1096474143869898792, 5],
        'severin-questions'         =>  [1072658926362959913, 1096474234395570298, 5],
    ];

    foreach($config_parse_channel as $channel) {

        $noDoublesMessages = getMessageDiscordChannel($channel[1], ['limit' => $channel[2], 'type' => 'list']);


        foreach(getMessageDiscordChannel($channel[0], ['limit' => $channel[2]]) as $id => $message) {
            if($message != '' && !in_array($id, $noDoublesMessages)) {
                $message = translateMessage($message);
                sendMessageDiscordChannel($channel[1], "#".$id."\r\n_Original:_ ".str_replace('\'', '', $message['src']).($message['src']!=$message['tsl']?"\r\n```Перевод: ".preg_replace('/http(s)*:(\/{2})[a-z0-9]+(\/*[-0-9a-z_\.\/%])*/i', '', $message['tsl'])."```":""));
                usleep(500000);
            }
        }
    }