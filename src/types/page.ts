export interface Page {
  id: string;
  slug: string;
  title: string;
  content: string;
  metaTitle: string | null;
  metaDescription: string | null;
  createdAt: string | Date;
  updatedAt: string | Date;
}

export interface PageFormData {
  slug: string;
  title: string;
  content: string;
  metaTitle: string;
  metaDescription: string;
}
