import { useState } from 'react'
import { TProductVariationsSelectorParams } from './index'
import { TCourseRecord } from '@/pages/admin/Courses/CourseSelector/types'

type TProductVariationsSelectorProps = Omit<
  TProductVariationsSelectorParams,
  'record'
>

type TUseProductVariationsSelector = (record: TCourseRecord) => {
  productVariationsSelectorProps: TProductVariationsSelectorProps
}

export const useProductVariationsSelector: TUseProductVariationsSelector = (
  record,
) => {
  const [selectedAttributes, setSelectedAttributes] = useState<
    { name: string; value: string }[]
  >([])

  const children = record?.children || []

  const selectedVariation = children.find(({ attributes }) => {
    const allAttributeKeys = Object.keys(attributes)
    if (selectedAttributes?.length !== allAttributeKeys.length) {
      return false
    }
    return allAttributeKeys.every((attributeKey) => {
      return (
        attributes?.[attributeKey] ===
        selectedAttributes.find((attr) => attr.name === attributeKey)?.value
      )
    })
  })

  return {
    productVariationsSelectorProps: {
      selectedVariation,
      selectedAttributes,
      setSelectedAttributes,
    },
  }
}
