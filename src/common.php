<?php

\think\Console::addDefaultCommands([
    "think\\queue\\command\\Work",
    "think\\queue\\command\\Restart",
    "think\\queue\\command\\Listen",
    "think\\queue\\command\\Subscribe"
]);
