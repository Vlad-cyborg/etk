<?php

JError::raiseNotice(1, "For update Joomshopping use Joomla.<br>
Download Joomshopping 4.16.0.<br>
Joomla / Extensions / Manage / Install");

JFactory::getApplication()->redirect('index.php?option=com_jshopping&controller=info');