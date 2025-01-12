# VkTgParser
Автоматический перепост записей из ВК в Telegram. Работает на основе [Callback API](https://dev.vk.com/ru/api/callback/getting-started). index.php устанавливается как endpoint для Callback API на события о новой записи в группе.

## Config.php
- `CONFIRM_CODE` - confirmation_code из настроек API группы ВК
- `SECRET` - секретный ключ из настроек API группы ВК
- `BOT_TOKEN` - токен доступа к боту Telegram, от имени которого будут делаться посты в канале
- `CHAT_ID` - id канала в Telegram, в который будут делаться посты
- `VK_TOKEN` - ВКшный access_token, полученный через любой официальный клиент (лучше всего работает Kate Mobile), чтобы можно было перепощивать аудиозаписи (можно указать токен любого другого приложения, но тогда аудио в постах не будет)
- `API_V` - версия VK API. Проверялось только на 5.131
