
# Telegram бот із інтеграцією Trello

## Встановлення

### Попередні вимоги

- PHP >= 8.2
- Composer
- Telegram Bot Token
- Trello API Key, Token BoardId

### Налаштування

1. **Клонування репозиторію**:
   ```bash
   git clone https://github.com/Otaku-First/YEDUCODERS_TEST_BOT.git
   cd YEDUCODERS_TEST_BOT
   ```

2. **Встановлення залежностей**:
      ```bash
      composer install
      ```

3. **Налаштування змінних середовища**:
    - Створіть файл `.env` на основі `.env.example`:
      ```bash
      cp .env.example .env
      ```
    - Налаштуйте підключення до БД  
    - Додайте до файлу `.env` наступні параметри:
      ```env
      TELEGRAM_BOT_TOKEN=ваш-telegram-токен
      TELEGRAM_GROUP_ID=id-групи-в-telegram
      TRELLO_API_KEY=ваш-trello-api-key
      TRELLO_API_TOKEN=ваш-trello-api-token
      BOARD_ID=id-дошки-в-trello
      ```

4. **Запуск міграцій**:
   ```bash
   php artisan migrate
   ```

5. **Налаштування webhook для Telegram**:
      ```bash
      php artisan telegram:webhook --setup
      ```

6. **Налаштування webhook для Trello**:
      ```bash
      php artisan trello:setup 
      ```
---



## Розробка

### Тестування локально

Для тестування локально використовуйте [ngrok](https://ngrok.com/):
```bash
  ngrok http 8000
```
Після цього оновіть `APP_URL` в `.env` на отриманий з ngrok та виконайте команди:
```bash
  php artisan telegram:webhook --setup
  php artisan trello:setup 
```



