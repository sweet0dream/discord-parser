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
        //category: video library
        'lesson-strategies'             =>  [1042500290676531220, 1096513986679283764, 100],
        'daily-screenshare-sessions'    =>  [1042500423916990546, 1096514047475724289, 100],
        'preweek-screenshare-sessions'  =>  [1042500567693537421, 1096514105331957850, 100],
        'preweek-voice-sessions'        =>  [1042500631270801409, 1096514162210910401, 100],
        'midweek-market-updates'        =>  [1042500705317027840, 1096514218477502536, 100],
        'altcoin-sessions'              =>  [1042500751504715797, 1096514271619338250, 100],
        'elliot-wave-sessions'          =>  [1042500808429813781, 1096514323737743390, 100],
        'psychology-sessions'           =>  [1042500860728594492, 1096514390297153586, 100],
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