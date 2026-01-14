export enum ReferenceType {
	Web = "web",
	Book = "book",
};

export interface Reference {
	type: ReferenceType;
	key(): string;
	label(): string;
	toHayagriva(): Object;
};

function getFirstWord(name: string): string {
	const parts = name.split(" ");
	return parts[0];
}

function getLastWord(name: string): string {
	const parts = name.split(" ");
	return parts[parts.length - 1];
}

export class WebReference implements Reference {
	type = ReferenceType.Web;

	constructor(
		readonly url: string,
		readonly title: string,
		readonly visited: Date,
		readonly date: Date,
		readonly author?: string,
	) {}

	static from(ref: WebReference): WebReference {
		return new WebReference(
			ref.url,
			ref.title,
			ref.visited,
			ref.date,
			ref.author
		);
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
			type: this.type,
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

