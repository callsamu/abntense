<?php

namespace App\Data\Reference;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ReferenceData extends Data
{
    public function __construct(
        public EntryType $type,
        public string $title,

        /** @var Collection<PersonData> */
        public DataCollection $author,

        public CarbonImmutable|string|null $date = null,
        public ?int $year = null,

        /** @var DataCollection<string>|null */
        public ?DataCollection $editor = null,

        public int|string|null $month = null,
        public ?string $publisher = null,
        public ?string $location = null,
        public ?string $language = null,
        public ?string $note = null,
        public ?string $url = null,
        public ?string $doi = null,
        public ?string $isbn = null,
        public ?string $issn = null,
        public ?array $keywords = null,
        public ?string $serial = null,
        public int|string|null $volume = null,
        public int|string|null $issue = null,
        public int|string|null $edition = null,
        public ?string $pages = null,
        public ?string $pageRange = null, // maps "page-range"
        public ?string $organization = null,

        // Article-specific
        public ?string $journal = null,

        // Book-specific
        public ?string $series = null,

        // Web-specific
        public ?CarbonImmutable $accessed = null,
    ) {}
}

