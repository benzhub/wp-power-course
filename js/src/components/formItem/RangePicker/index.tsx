import { FC } from 'react'
import { Form, DatePicker, GetProps, FormItemProps } from 'antd'
import { parseRangePickerValue } from '@/utils'

const { Item } = Form
type RangePickerProps = GetProps<typeof DatePicker.RangePicker>
const { RangePicker: AntdRangePicker } = DatePicker

export const RangePicker: FC<{
  formItemProps?: FormItemProps
  rangePickerProps?: RangePickerProps
}> = ({ formItemProps, rangePickerProps }) => {
  return (
    <Item
      getValueProps={(values) => ({
        value: parseRangePickerValue(values) as unknown as Record<
          string,
          unknown
        >,
      })}
      {...formItemProps}
    >
      <AntdRangePicker
        className="w-full"
        allowEmpty={[true, true]}
        format="YYYY-MM-DD HH:mm"
        {...rangePickerProps}
      />
    </Item>
  )
}
