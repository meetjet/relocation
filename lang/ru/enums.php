<?php

use App\Enums\EntityStatus;
use App\Enums\EntityVisibility;
use App\Enums\ExportEntity;
use App\Enums\ExportType;
use App\Enums\FairyGamesUserType;
use App\Enums\ProjectPreset;
use App\Enums\ProjectType;
use App\Enums\Role;
use App\Enums\ShopProductColor;
use App\Enums\ShopProductSize;
use App\Enums\TransactionStatus;
use App\Enums\VideoProvider;
use App\Enums\WalletReasonMain;
use App\Enums\WalletReasonSystem;
use App\Enums\WalletTransactionType;
use WeconfModules\Core\Enums\PageTemplateOption;
use WeconfModules\Core\Enums\TotalizatorBusinessOptions;
use WeconfModules\Core\Enums\UniversalMediaStatus;
use WeconfModules\Core\Enums\UniversalMediaType;
use WeconfModules\Core\Enums\UserResultStatus;
use WeconfModules\Core\Enums\UserResultType;
use WeconfModules\Quiz\Enums\QuizQuestionType;

return [
    WalletTransactionType::class => [
        WalletTransactionType::DEPOSIT => 'Начисление',
        WalletTransactionType::WITHDRAW => 'Расход',
    ],
    WalletReasonMain::class => [
        WalletReasonMain::CORRECTION => 'Коррекция',
        WalletReasonMain::CARD => 'Открытка',
        WalletReasonMain::QUIZ => 'Викторина',
        WalletReasonMain::INSTAGRAM => 'Инстаграм',
        WalletReasonMain::VKONTAKTE => 'ВКонтакте',
        WalletReasonMain::CROSSWORD => 'Кроссворд',
        WalletReasonMain::FILLWORD => 'Филворд',
        WalletReasonMain::CHECKERS => 'Шашки',
        WalletReasonMain::CHESS => 'Шахматы',
        WalletReasonMain::SPORTS_CHALLENGE => 'Спортивный челлендж',
        WalletReasonMain::MIND_FITNESS => 'Майнд фитнес',
        WalletReasonMain::GIFT_WRAPPING => 'Упаковка подарков',
        WalletReasonMain::BIRTHDAY => 'День варенья',
        WalletReasonMain::FOOTBALL_FREESTYLE => 'Футбольный фристайл',
        WalletReasonMain::BELLY_DANCE => 'Танец живота',
        WalletReasonMain::TRAVELS => 'Путешествия',
        WalletReasonMain::QUILLING => 'Квиллинг',
        WalletReasonMain::POWER_TRAINING => 'Power Training', // No translation required
        WalletReasonMain::FASHION_SCIENCE => 'Фэшн-наука',
        WalletReasonMain::JUMP_STYLE => 'Jump style', // No translation required
        WalletReasonMain::FLORISTICS => 'Флористика',
        WalletReasonMain::GRAFFITI => 'Граффити',
        WalletReasonMain::FIT_XIKI => 'Fit-xiki', // No translation required
        WalletReasonMain::CHILD_SUIT => 'Детский костюм',
        WalletReasonMain::VOLUNTEERING => 'Волонтерство',
        WalletReasonMain::SPORT => 'Спорт',
        WalletReasonMain::CREATIVITY => 'Творчество',
        WalletReasonMain::GTO => 'ГТО',
        WalletReasonMain::BUSINESS => 'Бизнес',
        WalletReasonMain::MIND => 'Ум',
        WalletReasonMain::REGISTRATION => 'Регистрация',
        WalletReasonMain::RESULT_UPLOAD => 'Загрузка результата',
        WalletReasonMain::PHOTO => 'Загрузка фото',
        WalletReasonMain::VIDEO => 'Загрузка видео',
        WalletReasonMain::RECOMMENDATIONS => 'Рекомендации',
        WalletReasonMain::FUNFAIR => 'Ярмарка Добра',
        WalletReasonMain::DISCREPANCY => 'Несоответствие заданию',
    ],
    WalletReasonSystem::class => [
        WalletReasonSystem::TOTALIZATOR_BET => 'Ставка в тотализаторе',
        WalletReasonSystem::TOTALIZATOR_WIN => 'Выигрыш в тотализаторе',
        WalletReasonSystem::TOTALIZATOR_REFUND => 'Возврат ставки в тотализаторе',
        WalletReasonSystem::BONUS_ONE => 'Bonus one', // TODO
        WalletReasonSystem::GLOBAL_POLL => 'Global poll', // TODO
        WalletReasonSystem::FIRST_LOGIN => 'Первый вход',
        WalletReasonSystem::TIMETABLE_ACTIVATED => 'Timetable activated', // TODO
        WalletReasonSystem::PURPOSE => 'Purpose', // TODO
        WalletReasonSystem::IMPORT => 'Import', // TODO
        WalletReasonSystem::SEEDER => 'Seeder', // TODO
        WalletReasonSystem::CORRECT_REGISTRATION => 'Correct registration', // TODO
        WalletReasonSystem::LIKES => 'Лайки',
        WalletReasonSystem::FILLED_PHONE => 'Указан телефон',
        WalletReasonSystem::DAILY_VISIT => 'Ежедневное посещение',
        WalletReasonSystem::TOAST_CLICK => 'Поймай Енота',
        WalletReasonSystem::TRANSFER => 'Перевод',
        WalletReasonSystem::PURCHASE => 'Покупка',
    ],
    EntityStatus::class => [
        EntityStatus::PUBLISHED => 'Опубликовано',
        EntityStatus::PENDING => 'В ожидании',
        EntityStatus::DRAFT => 'Черновик',
    ],
    EntityVisibility::class => [
        EntityVisibility::PUBLIC => 'Общедоступный',
        EntityVisibility::PASSWORD => 'По паролю',
        EntityVisibility::PRIVATE => 'Приватный',
    ],
    TransactionStatus::class => [
        TransactionStatus::PENDING => 'В ожидании',
        TransactionStatus::CONFIRMED => 'Подтвержденный',
        TransactionStatus::REJECTED => 'Отклоненный',
    ],
    QuizQuestionType::class => [
        QuizQuestionType::DATE => 'Дата',
        QuizQuestionType::DROPDOWN => 'Выпадающий список',
        QuizQuestionType::EMAIL => 'Email',
        QuizQuestionType::FILE => 'Файл',
        QuizQuestionType::LONG_TEXT => 'Длинный текст',
        QuizQuestionType::MULTIPLE_CHOICE => 'Множественный выбор',
        QuizQuestionType::MULTIPLE_PICTURE_CHOICE => 'Выбор нескольких изображений',
        QuizQuestionType::NUMBER => 'Число',
        QuizQuestionType::PASSWORD => 'Пароль',
        QuizQuestionType::PHONE => 'Телефон',
        QuizQuestionType::SECTION_BREAK => 'Разделитель',
        QuizQuestionType::TEXT => 'Текст',
        QuizQuestionType::URL => 'Url',
    ],
    UserResultStatus::class => [
        UserResultStatus::PAID => 'Оплачено',
        UserResultStatus::PENDING => 'В ожидании',
        UserResultStatus::REJECTED => 'Отклонено',
    ],
    UserResultType::class => [
        UserResultType::IMAGE => 'Изображение',
        UserResultType::VIDEO => 'Видео',
    ],
    ShopProductColor::class => [
        ShopProductColor::RED => 'Красный',
        ShopProductColor::WHITE => 'Белый',
        ShopProductColor::ORANGE => 'Оранжевый',
        ShopProductColor::CYAN => 'Голубой',
        ShopProductColor::BLUE => 'Синий',
        ShopProductColor::GREEN => 'Зелёный',
        ShopProductColor::GRAY => 'Серый',
//        ShopProductColor::BLACK => 'Чёрный',
        ShopProductColor::YELLOW => 'Жёлтый',
        ShopProductColor::PURPLE => 'Фиолетовый',
        ShopProductColor::TURQUOISE => 'Бирюзовый',
        ShopProductColor::YELLOW_GREEN => 'Салатовый',
    ],
    ShopProductSize::class => [
        ShopProductSize::S => 'S',
        ShopProductSize::M => 'M',
        ShopProductSize::L => 'L',
        ShopProductSize::XL => 'XL',
        ShopProductSize::XXL => 'XXL',
    ],
    FairyGamesUserType::class => [
        FairyGamesUserType::EMPLOYEE => 'Сотрудник',
        FairyGamesUserType::REFERRAL => 'Реферал',
        FairyGamesUserType::STUDENT => 'Студент',
    ],
    Role::class => [
        Role::GOD => 'Супер администратор',
        Role::ADMINISTRATOR => 'Администратор',
        Role::CUSTOMER => 'Клиент',
        Role::MANAGER => 'Менеджер',
        Role::EDITOR => 'Редактор',
        Role::AUTHOR => 'Автор',
        Role::MEMBER => 'Участник',
        Role::SUBSCRIBER => 'Подписчик',
    ],
    ProjectType::class => [
        ProjectType::PUBLIC => 'Публичное мероприятие',
        ProjectType::PRIVATE => 'Закрытое мероприятие',
    ],
    ProjectPreset::class => [
        ProjectPreset::DEFAULT => 'По умолчанию',
        ProjectPreset::RADIO_DAY => 'День Радио',
    ],
    PageTemplateOption::class => [
        PageTemplateOption::DEFAULT => 'По умолчанию',
        PageTemplateOption::PLAYER => 'Плеер',
        PageTemplateOption::PLAYER_WITH_CHAT => 'Плеер с чатом',
        PageTemplateOption::PLAYER_WITH_CHAT_AND_UPLOAD_FORM => 'Плеер с чатом и формой загрузки',
//        PageTemplateOption::CONTENT_WITH_VIDEO_UPLOAD => 'Контент с загрузкой видео',
//        PageTemplateOption::CONTENT_WITH_PICTURE_UPLOAD => 'Контент с загрузкой изображений',
//        PageTemplateOption::PICTURES_WITH_LIKES => 'Стена изображений с лайками',
//        PageTemplateOption::VIDEOS_WITH_LIKES => 'Стена видео с лайками',
    ],
    UniversalMediaStatus::class => [
        UniversalMediaStatus::PENDING => 'В ожидании',
        UniversalMediaStatus::PAID => 'Оплачено',
        UniversalMediaStatus::REJECTED => 'Отклонено',
    ],
    UniversalMediaType::class => [
        UniversalMediaType::IMAGE => 'Изображение',
        UniversalMediaType::VIDEO => 'Видео',
    ],
    VideoProvider::class => [
        VideoProvider::FACECAST => 'Facecast',
        VideoProvider::MUX => 'Mux',
        VideoProvider::VIMEO => 'Vimeo',
        VideoProvider::RUTUBE => 'Rutube',
        VideoProvider::YOUTUBE => 'Youtube',
        VideoProvider::VK => 'VK',
    ],
    TotalizatorBusinessOptions::class => [
        TotalizatorBusinessOptions::AGRO => 'Сельскохозяйственный бизнес',
        TotalizatorBusinessOptions::MEAT => 'Мясной бизнес',
        TotalizatorBusinessOptions::SUGAR => 'Сахарный бизнес',
        TotalizatorBusinessOptions::OIL => 'Масложировой бизнес',
        TotalizatorBusinessOptions::GROUP_AND_SERVICE => 'ГК и ОЦО',
    ],
    ExportType::class => [
        ExportType::DATA => "Данные",
        ExportType::FILES => "Файлы",
    ],
    ExportEntity::class => [
        ExportEntity::USERS => "Пользователи",
        ExportEntity::SHOP_STOCK => "Магазин: склад",
        ExportEntity::SHOP_ORDERS => "Магазин: заказы",
        ExportEntity::USER_MEDIA => "Медиа",
        ExportEntity::FUNFAIR => "Ярмарка Добра",
        ExportEntity::RECOMMENDATIONS => "Рекомендации",
    ],
];
