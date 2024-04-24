<?php
class StylerMsg implements StylerMsgInterface 
{
    private array $keepButtons;
    public string $msgForParse;


    public function __construct(string $msgForParse) 
    {
        $this->msgForParse = $msgForParse;
    }



    private function CheckingButtons(array $keepButtons) : array
    {
        foreach ($keepButtons as &$stringButtons) {
            $stringButtons = str_replace( ["(", ")"], "", $stringButtons );
            for ($offSetCount = 0; 2 > $offSetCount; $offSetCount++) {
                $divideInfo = explode( "||", $stringButtons );

                if (stristr($divideInfo[$offSetCount], "http")) {
                    $nameButton = $offSetCount == 0 ? $divideInfo[1] : $divideInfo[0];
                    $keepInfo = ["text" => $nameButton, "url" => $divideInfo[$offSetCount]];
                    continue 2;
                }

                $keepInfo = ["text" => $divideInfo[0], "callback_data" => $divideInfo[1]];
            }
            $stringButtons = $keepInfo;
        }
        return $keepButtons;
    }



    public function ParseButtons() : self
    {
        if (preg_match_all("#\\([\S ]+\\|\\|[\S ]+\\)#", $this->msgForParse, $matchButtons)) {
            $this->msgForParse = str_replace( $matchButtons[0], "", $this->msgForParse );
            $this->keepButtons = $this->CheckingButtons( $matchButtons[0] );
        }
        return $this;
    }



    public function ProcessVar() : self 
    {
        if (preg_match_all("\\$[A-Z]\w+", $this->msgForParse, $matchVarsLiterally)) {

            foreach ($matchVarsLiterally[0] as $varsLiterally) {
                $nameVars = str_replace( "\$", "", $varsLiterally );
                $valueReplace = isset( $GLOBALS[$nameVars] ) ? $GLOBALS[$nameVars] : $varsLiterally;
                $this->msgForParse = str_replace( $varsLiterally, $valueReplace, $this->msgForParse );
            }
        }
        
        return $this;
    }
}
