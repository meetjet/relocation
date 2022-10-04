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
        WalletTransactionType::DEPOSIT => 'Deposit',
        WalletTransactionType::WITHDRAW => 'Withdraw',
    ],
    WalletReasonMain::class => [
        WalletReasonMain::CORRECTION => 'Correction',
        WalletReasonMain::CARD => 'Card',
        WalletReasonMain::QUIZ => 'Quiz',
        WalletReasonMain::INSTAGRAM => 'Instagram',
        WalletReasonMain::VKONTAKTE => 'VKontakte',
        WalletReasonMain::CROSSWORD => 'Crossword',
        WalletReasonMain::FILLWORD => 'Fillword',
        WalletReasonMain::CHECKERS => 'Checkers',
        WalletReasonMain::CHESS => 'Chess',
        WalletReasonMain::SPORTS_CHALLENGE => 'Sports challenge',
        WalletReasonMain::MIND_FITNESS => 'Mind fitness',
        WalletReasonMain::GIFT_WRAPPING => 'Gift wrapping',
        WalletReasonMain::BIRTHDAY => 'Birthday',
        WalletReasonMain::FOOTBALL_FREESTYLE => 'Football freestyle',
        WalletReasonMain::BELLY_DANCE => 'Belly dance',
        WalletReasonMain::TRAVELS => 'Travels',
        WalletReasonMain::QUILLING => 'Quilling',
        WalletReasonMain::POWER_TRAINING => 'Power Training',
        WalletReasonMain::FASHION_SCIENCE => 'Fashion science',
        WalletReasonMain::JUMP_STYLE => 'Jump style',
        WalletReasonMain::CHILD_SUIT => 'Child suit',
        WalletReasonMain::FLORISTICS => 'Floristics',
        WalletReasonMain::GRAFFITI => 'Graffiti',
        WalletReasonMain::FIT_XIKI => 'Fit-xiki',
        WalletReasonMain::VOLUNTEERING => 'Volunteering',
        WalletReasonMain::SPORT => 'Sport',
        WalletReasonMain::CREATIVITY => 'Creativity',
        WalletReasonMain::GTO => 'GTO', // TODO
        WalletReasonMain::BUSINESS => 'Business',
        WalletReasonMain::MIND => 'Mind',
        WalletReasonMain::REGISTRATION => 'Registration',
        WalletReasonMain::RESULT_UPLOAD => 'Result upload',
        WalletReasonMain::PHOTO => 'Photo upload',
        WalletReasonMain::VIDEO => 'Video upload',
        WalletReasonMain::RECOMMENDATIONS => 'Recommendations',
        WalletReasonMain::FUNFAIR => 'Funfair',
        WalletReasonMain::DISCREPANCY => 'Discrepancy',
    ],
    WalletReasonSystem::class => [
        WalletReasonSystem::TOTALIZATOR_BET => 'Bet in the totalizator',
        WalletReasonSystem::TOTALIZATOR_WIN => 'Winning in the totalizator',
        WalletReasonSystem::TOTALIZATOR_REFUND => 'Refund of bet in the totalizator',
        WalletReasonSystem::BONUS_ONE => 'Bonus one', // TODO
        WalletReasonSystem::GLOBAL_POLL => 'Global poll', // TODO
        WalletReasonSystem::FIRST_LOGIN => 'First login',
        WalletReasonSystem::TIMETABLE_ACTIVATED => 'Timetable activated', // TODO
        WalletReasonSystem::PURPOSE => 'Purpose', // TODO
        WalletReasonSystem::IMPORT => 'Import', // TODO
        WalletReasonSystem::SEEDER => 'Seeder', // TODO
        WalletReasonSystem::CORRECT_REGISTRATION => 'Correct registration', // TODO
        WalletReasonSystem::LIKES => 'Likes',
        WalletReasonSystem::FILLED_PHONE => 'Filled phone',
        WalletReasonSystem::DAILY_VISIT => 'Daily visit',
        WalletReasonSystem::TOAST_CLICK => 'Toast click',
        WalletReasonSystem::TRANSFER => 'Transfer',
        WalletReasonSystem::PURCHASE => 'Purchase',
    ],
    EntityStatus::class => [
        EntityStatus::PUBLISHED => 'Published',
        EntityStatus::PENDING => 'Pending',
        EntityStatus::DRAFT => 'Draft',
    ],
    EntityVisibility::class => [
        EntityVisibility::PUBLIC => 'Public',
        EntityVisibility::PASSWORD => 'Password',
        EntityVisibility::PRIVATE => 'Private',
    ],
    TransactionStatus::class => [
        TransactionStatus::PENDING => 'Pending',
        TransactionStatus::CONFIRMED => 'Confirmed',
        TransactionStatus::REJECTED => 'Rejected',
    ],
    QuizQuestionType::class => [
        QuizQuestionType::DATE => 'Date',
        QuizQuestionType::DROPDOWN => 'Dropdown',
        QuizQuestionType::EMAIL => 'Email',
        QuizQuestionType::FILE => 'File',
        QuizQuestionType::LONG_TEXT => 'Long text',
        QuizQuestionType::MULTIPLE_CHOICE => 'Multiple choice',
        QuizQuestionType::MULTIPLE_PICTURE_CHOICE => 'Multiple picture choice',
        QuizQuestionType::NUMBER => 'Number',
        QuizQuestionType::PASSWORD => 'Password',
        QuizQuestionType::PHONE => 'Phone',
        QuizQuestionType::SECTION_BREAK => 'Section break',
        QuizQuestionType::TEXT => 'Text',
        QuizQuestionType::URL => 'Url',
    ],
    UserResultStatus::class => [
        UserResultStatus::PAID => 'Paid',
        UserResultStatus::PENDING => 'Pending',
        UserResultStatus::REJECTED => 'Rejected',
    ],
    UserResultType::class => [
        UserResultType::IMAGE => 'Image',
        UserResultType::VIDEO => 'Video',
    ],
    ShopProductColor::class => [
        ShopProductColor::RED => 'Red',
        ShopProductColor::WHITE => 'White',
        ShopProductColor::ORANGE => 'Orange',
        ShopProductColor::CYAN => 'Cyan',
        ShopProductColor::BLUE => 'Blue',
        ShopProductColor::GREEN => 'Green',
        ShopProductColor::GRAY => 'Gray',
//        ShopProductColor::BLACK => 'Black',
        ShopProductColor::YELLOW => 'Yellow',
        ShopProductColor::PURPLE => 'Purple',
        ShopProductColor::TURQUOISE => 'Turquoise',
        ShopProductColor::YELLOW_GREEN => 'Yellow-green',
    ],
    ShopProductSize::class => [
        ShopProductSize::S => 'S',
        ShopProductSize::M => 'M',
        ShopProductSize::L => 'L',
        ShopProductSize::XL => 'XL',
        ShopProductSize::XXL => 'XXL',
    ],
    FairyGamesUserType::class => [
        FairyGamesUserType::EMPLOYEE => 'Employee',
        FairyGamesUserType::REFERRAL => 'Referral',
        FairyGamesUserType::STUDENT => 'Student',
    ],
    Role::class => [
        Role::GOD => 'Super administrator',
        Role::ADMINISTRATOR => 'Administrator',
        Role::CUSTOMER => 'Customer',
        Role::MANAGER => 'Manager',
        Role::EDITOR => 'Editor',
        Role::AUTHOR => 'Author',
        Role::MEMBER => 'Member',
        Role::SUBSCRIBER => 'Subscriber',
    ],
    ProjectType::class => [
        ProjectType::PUBLIC => 'Public event',
        ProjectType::PRIVATE => 'Private event',
    ],
    ProjectPreset::class => [
        ProjectPreset::DEFAULT => 'Default',
        ProjectPreset::RADIO_DAY => 'Radio Day',
    ],
    PageTemplateOption::class => [
        PageTemplateOption::DEFAULT => 'Default',
        PageTemplateOption::PLAYER => 'Player',
        PageTemplateOption::PLAYER_WITH_CHAT => 'Player with chat',
        PageTemplateOption::PLAYER_WITH_CHAT_AND_UPLOAD_FORM => 'Player with chat and media upload',
//        PageTemplateOption::CONTENT_WITH_VIDEO_UPLOAD => 'Content with video upload',
//        PageTemplateOption::CONTENT_WITH_PICTURE_UPLOAD => 'Content with picture upload',
//        PageTemplateOption::PICTURES_WITH_LIKES => 'Pictures with likes',
//        PageTemplateOption::VIDEOS_WITH_LIKES => 'Videos with likes',
    ],
    UniversalMediaStatus::class => [
        UniversalMediaStatus::PENDING => 'Pending',
        UniversalMediaStatus::PAID => 'Paid',
        UniversalMediaStatus::REJECTED => 'Rejected',
    ],
    UniversalMediaType::class => [
        UniversalMediaType::IMAGE => 'Image',
        UniversalMediaType::VIDEO => 'Video',
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
        TotalizatorBusinessOptions::AGRO => 'Agricultural business',
        TotalizatorBusinessOptions::MEAT => 'Meat business',
        TotalizatorBusinessOptions::SUGAR => 'Sugar business',
        TotalizatorBusinessOptions::OIL => 'Fat and oil business',
        TotalizatorBusinessOptions::GROUP_AND_SERVICE => 'GK and OCO',
    ],
    ExportType::class => [
        ExportType::DATA => "Data",
        ExportType::FILES => "Files",
    ],
    ExportEntity::class => [
        ExportEntity::USERS => "Users",
        ExportEntity::SHOP_STOCK => "Shop stock",
        ExportEntity::SHOP_ORDERS => "Shop orders",
        ExportEntity::USER_MEDIA => "User media",
        ExportEntity::FUNFAIR => "Funfair",
        ExportEntity::RECOMMENDATIONS => "Recommendations",
    ],
];
