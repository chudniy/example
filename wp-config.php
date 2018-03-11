<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'wordpress');

/** Имя пользователя MySQL */
define('DB_USER', 'www');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '123456');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');
define('FS_METHOD', 'direct');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '-s;`6TforxJb*f1l*pexA-}N_t7y8:?WO.1`~{^DgHK]s}Zr|R*F7{1_%41|v?R;');
define('SECURE_AUTH_KEY',  '6GNyx~`z(jA<1o,RT6u8o]3o<tkNiC.lxa& mLKRC8#U(K9f{lZhBb)6nR$kVIc/');
define('LOGGED_IN_KEY',    'k4-l&^9Yz_X`feL,TV/To>Y`*TN: ;oJjX)9<QzF4r$76 awp)VDeUr:Fks(yuUr');
define('NONCE_KEY',        '~J<hrj<4.&EcO$<R#Vcn4d, vcW tSzjYBv)pyOsD5>^QU0-fpG-fM)nM#F<$YAQ');
define('AUTH_SALT',        '<k98fgpU0/f+)hS9ITa+PS7<8U[Y})[=dBPEL2GyqxJe(R=gF5RwI9N#@R4MZOU/');
define('SECURE_AUTH_SALT', 'n3_[4zz5S?)ILGhT;s$*;(jYl63K}:?`i7IQQ}jnEa|;Ab4u!a78#k7vo<DkdGfa');
define('LOGGED_IN_SALT',   'n,y=E!Wtse^>013fxmn<[u 93MFfZzQd/gqygBuY/T,p*paa{l=EQ^/>g)K_s7R|');
define('NONCE_SALT',       'VB^f]jvD*NG8#,H-^f[H&|wa5)$0XcSGSlIp9Jd_HDv=dW?fKO|b(FQqW.?hLPnn');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');

