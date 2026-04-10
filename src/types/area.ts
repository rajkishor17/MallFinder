export interface Area {
  id: string;
  name: string;
  cityId?: string | null;
  city?: {
    id: string;
    name: string;
    state?: {
      id: string;
      name: string;
    };
  } | null;
  createdAt: string;
  updatedAt: string;
}
