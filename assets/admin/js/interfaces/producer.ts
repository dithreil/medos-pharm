export interface IProducer {
    id: string,
    shortName: string,
    fullName: string,
    country: string,
}

export interface IProducerDetails extends IProducer{
    createdAt: string,
    updatedAt: string
}

export interface IProducerData {
    total: number | null,
    pages: number | null,
    limit: number | null,
    page: number | null,
    prev: number | null,
    next: number | null,
    items: Array<IProducer> | null
}
