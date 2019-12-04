<?php

/*просто убивает сессию (+вычищает куки), что за время в time() не разбирался*/
$sessionLifetime = 60 * 60 * 24 * 5; //5 дней
ini_set('session.gc_maxlifetime', $sessionLifetime); //установку времени для сборщика мусора временно отключил
ini_set('session.cookie_lifetime', $sessionLifetime); //0 - значит до закрытия браузера. формула 60*60*24*5 (5 дней)

session_start();

function destroySession()
{
   if (session_id()) {
      session_unset();
      setcookie(session_name(), session_id(), time() - 60 * 60 * 24 * 5);
      session_destroy();
   }
}
