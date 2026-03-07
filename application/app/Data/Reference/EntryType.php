<?php

namespace App\Data\Reference;

enum EntryType: string
{
    case Article = 'article';
    case Book = 'book';
    case Web = 'web';
}
