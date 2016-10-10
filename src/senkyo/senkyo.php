<?php

namespace senkyo;


  use pocketmine\utils\Config;
  use pocketmine\command\Command;
  use pocketmine\command\CommandSender;
  use pocketmine\Player;
  use pocketmine\Server;
  use pocketmine\event\player\PlayerJoinEvent;
  use pocketmine\event\Listener;
  use pocketmine\plugin\PluginBase;
  use pocketmine\utils\TextFormat;

 

 class senkyo extends PluginBase implements Listener{


 public function onEnable(){

   if(!file_exists($this->getDataFolder())){
        mkdir($this->getDataFolder(), 0744,true);

}

   $this->botan = new Config($this->getDataFolder() ."botan.yml", Config::YAML,
   array(
         "senkyo" => "off"
        ));
  
   $this->rikkouho = new Config($this->getDataFolder() ."rikkouho.yml",Config::YAML,array());

   $this->yuuken = new Config($this->getDataFolder() ."yuuken.yml",Config::YAML,array());


   $this->botan->save();
   $this->rikkouho->save();
   $this->yuuken->save();


   $this->getServer()->getPluginManager()->registerEvents($this,$this);
 }

 
   public function onJoin(PlayerJoinEvent $ev){

       $player = $ev->getPlayer();

       if($this->botan->get("senkyo") == "on"){
  
            $player->sendMessage("[§b選挙§f] §a現在投票期間中です。/rhs で立候補者を確認しましょう.");

        }


  

}


   public function onCommand(CommandSender $sender, Command $command,$lavel, array $args){
              switch($command->getName()){

             case "senkyo":

                          if(isset($args[0])){

                               switch($args[0]){

                                     case "on":

                                              $this->botan->set("senkyo","on");
                                              $this->botan->save();
                                              $this->getServer()->broadcastMessage("[§b選挙§f] 選挙が開始されました");
                                      return true;
                                      break;

                                      case "off":
 
                                                 $this->botan->set("senkyo","off");
                                                 $this->botan->save();
                                                 $this->getServer()->broadcastMessage("[§b選挙§f] 選挙がおわりました");
                                                 return true;
                                                 break;

                                      default:
                                              $sender->sendMessage("[§b選挙§f] onかoffかを選択してください");

                                 }
                          }else{

                                $sender->sendMessage("[§b選挙§f] onかoffか選択してください");

                          }
                   return true;
                   break;



                case "rikkouho":

                               if($this->botan->get("senkyo") == "on"){

                                      $name = $sender->getName();

                                      if($this->rikkouho->exists($name)){

                                            $sender->sendMessage("[§b選挙§f] あなたはすでに立候補しています");

                                      }else{
            
                                             $this->rikkouho->set($name,0);
                                             $this->rikkouho->save();
                                             $sender->sendMessage("[§b選挙§f] 立候補しました");
                                      
                                      }
  
                               }else{

                                     $sender->sendMessage("[§b選挙§f] 現在選挙は行っておりません");

                                }

                        return true;
                        break;

                  case "touhyou":

                                 if($this->botan->get("senkyo") == "on"){

                                      if($this->yuuken->get($sender->getName()) == 0){

                                           if(isset($args[0])){
                                                  
                                                 
                                                 

                                                      $name = $args[0];

                                                       if($name == $sender->getName()){

                                                             $sender->sendMessage("[§b選挙§f] 自分に投票することは出来ません");

                                                        }else{

                                                              if($this->rikkouho->exists($name)){

                                                                     $kazu = $this->rikkouho->get($name);

                                                                     $kaz = $kazu + 1;
 
                                                                     $this->rikkouho->set($name,$kaz);
 
                                                                     $this->rikkouho->save();

                                                                     $sender->sendMessage("[§b選挙§f] ".$name."さんに投票しました");

                                                               $this->yuuken->set($sender->getName(),1);
                                                               $this->yuuken->save();
                                                                
                                                              }else{

                  
                                                                   $sender->sendMessage("[§b選挙§f] その人は立候補していないようです");

                                                      }
                                                    }
                                            }else{

                                                 $sender->sendMessage("[§b選挙§f] 立候補者から一人に投票してください");

                                                }
                                               }else{

                                                      $sender->sendMessage("[§b選挙§f] あなたは既に投票しています");

                                              }   
                                     }else{

                                            $sender->sendMessage("[§b選挙§f] 現在選挙は行っておりません");

                                      }

                               return true; 
                               break;


                               case "delall":

                                       if($sender->isOp()){

                                           foreach($this->rikkouho->getAll(true) as $r){
                                                  $this->rikkouho->remove($r);
                                                  $this->rikkouho->save();

                                           }

                                           foreach($this->yuuken->getAll(true) as $y){


                                                  $this->yuuken->remove($y);
                                                  $this->yuuken->save();

                                             }

                                          

                                         $this->getLogger()->notice("選挙のデータを削除しました");
                                         $sender->sendMessage(TextFormat::BLUE."選挙のデータを削除しました");

                                       }else{

                                          $sender->sendMessage("§c権限がありません");

                                        }

                                         return true;
                                         break;

                                                
 
                                       
                           

                                case "rhs":

                                          
                                        if($this->rikkouho->getAll() !=null){

                                           if($this->botan->get("senkyo") == "on"){


                                               if($sender->isOp()){

                                                          $data = $this->rikkouho->getAll(true);
                                               

                                               foreach($data as $player){


                                                          $sender->sendMessage("[§b選挙§f] ".$player."§6:§a".$this->rikkouho->get($player)."");
                                                     }
                                                }else{

                                               $data = $this->rikkouho->getAll(true);
                                                foreach($data as $player){
     
                                                $sender->sendMessage("[§a立候補者§f] ".$player."");

                                                      }
                                                    }
                                            }else{

                                               $data = $this->rikkouho->getAll(true);
                                               

                                               foreach($data as $player){

                                                      

                                                   $sender->sendMessage("[§b結果§f] ".$player."§6:§a".$this->rikkouho->get($player)."");



           

                                     }

                                }
                            }else {

                                   $sender->sendMessage("[§b選挙§f] まだ立候補者が居ないようです");

                               }          

                                           return true;
                                           break;
                                     
                }

              }
                                      

  
                                      
}