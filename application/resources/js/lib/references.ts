export enum ReferenceType {
	Web = "web",
	Book = "book",
};

export interface Reference {
	key(): string;
	label(): string;
	toHayagriva(): Object;
};

export interface ReferenceRecord {
    type: ReferenceType;
}

export interface WebRecord extends ReferenceRecord {
    type: ReferenceType.Web,
	url: string,
	title: string,
	visited: string,
	date: string,
	author?: string,
}

function getFirstWord(name: string): string {
	const parts = name.split(" ");
	return parts[0];
}

function getLastWord(name: string): string {
	const parts = name.split(" ");
	return parts[parts.length - 1];
}

export function recordToReference(r: ReferenceRecord) {
    switch (r.type) {
        case ReferenceType.Web:
            return new WebReference(r as WebRecord);
        default:
            throw new Error("unknown record type: " + r.type);
    }
}

export class WebReference implements Reference {
    private url: string;
    private title: string;
    private author?: string;
    private date: Date;
    private visited: Date;

	constructor(record: WebRecord) {
	    this.url = record.url;
	    this.author = record.author;
	    this.title = record.title;
	    this.date = new Date(record.date);
	    this.visited = new Date(record.visited);
	}

	key(): string {
		const year = this.date.getFullYear();
		const titleFirstWord = getFirstWord(this.title);

		if (this.author) {
			return `${titleFirstWord}-${getLastWord(this.author)}-${year}`;
		} else {
			return `${titleFirstWord}-${year}`;
		}
	}

	label(): string {
		const year = this.date.getFullYear();

		if (this.author) {
			return `${getLastWord(this.author)} (${year})`;
		} else {
			return `${getFirstWord(this.title)} (${year})`;
		}
	}

	toHayagriva(): Object {
		return {
			type: ReferenceType.Web,
			title: this.title,
			author: this.author,
			date: this.date.toISOString().split("T")[0],
			url: {
				value: this.url,
				date: this.visited.toISOString().split("T")[0],
			},
		};
	}
};

/*
export class BookReference implements Reference {
	type = ReferenceType.Book;

	constructor(
		readonly title: string,
		readonly authors: string[],
		readonly publisher: string,
		readonly date: Date,
	) {}

	static from(ref: Partial<BookReference>): BookReference {
		if (
			!ref.title ||
			!ref.authors ||
			!ref.publisher ||
			!ref.date
		) {
			throw new Error("invalid book reference");
		}

		return new BookReference(
			ref.title,
			ref.authors,
			ref.publisher,
			ref.date
		);
	}

	key(): string {
		const year = this.date.getFullYear();
		const titleFirstWord = getFirstWord(this.title);

		if (this.authors[0]) {
			return `${titleFirstWord}-${getLastWord(this.authors[0])}-${year}`;
		} else {
			return `${titleFirstWord}-${year}`;
		}
	}

	label(): string {
		const year = this.date.getFullYear();

		if (this.authors) {
			return `${getLastWord(this.authors[0])} (${year})`;
		} else {
			return `${getFirstWord(this.title)} (${year})`;
		}
	}

	toHayagriva(): Object {
		return {
			type: this.type,
			title: this.title,
			publisher: this.publisher,
			editor: this.publisher,
			author: this.authors,
			date: this.date.toISOString().split("T")[0],
		};
	}
};
*/
