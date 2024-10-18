import { Dayjs } from 'dayjs'

export type TFilterProps = Partial<{
	s: string
	sku: string
	product_category_id?: string[]
	product_tag_id?: string[]
	product_brand_id?: string[]
	status: string
	featured: boolean
	downloadable: boolean
	virtual: boolean
	sold_individually: boolean
	backorders: string
	stock_status: string
	date_created: [Dayjs, Dayjs]
	is_course: boolean
	price_range: [number, number]
}>

export type TTerm = {
	id: string
	name: string
}
export type TStockStatus = 'instock' | 'outofstock' | 'onbackorder'
export type TProductType =
	| 'simple'
	| 'variable'
	| 'grouped'
	| 'external'
	| 'subscription'
	| 'variable-subscription'
	| string

export type TProductAttribute = {
	name: string
	options: string[]
	position: number
}

export type TImage = {
	id: string
	url: string
}

export type TBindCoursesData = {
	id: string
	name: string
	limit_type: 'unlimited' | 'fixed' | 'assigned'
	limit_value: number
	limit_unit: 'second' | 'day' | 'month' | 'year'
}

export type TProductRecord = {
	id: string
	type: TProductType
	depth: number
	name: string
	slug: string
	date_created: string
	date_modified: string
	status: string
	featured: boolean
	catalog_visibility: string
	description: string
	short_description: string
	sku: string
	virtual: boolean
	downloadable: boolean
	permalink: string
	price_html: string
	regular_price: string
	sale_price: string
	on_sale: boolean
	total_sales: number
	stock: number | null
	stock_status: TStockStatus
	manage_stock: boolean
	stock_quantity: number | null
	backorders: 'yes'
	backorders_allowed: boolean
	backordered: boolean
	low_stock_amount: number | null
	upsell_ids: number[]
	cross_sell_ids: number[]
	variations: number[]
	attributes: TProductAttribute[]
	category_ids: string[]
	tag_ids: string[]
	images?: TImage[]
	children?: TProductVariation[]
	parent_id?: string
	pbp_product_ids: string[]
	sale_date_range: [number, number]
	is_free: 'yes' | 'no' | ''
	qa_list: {
		key: string
		question: string
		answer: string
	}[]
	bundle_type_label: string
	exclude_main_course: 'yes' | 'no' | ''
	bind_courses_data: TBindCoursesData[]
}

export type TProductVariation = TProductRecord & {
	type: TProductType | 'variation' | 'subscription_variation'
	parent_id: string
	attributes: { [key: string]: string }
}
