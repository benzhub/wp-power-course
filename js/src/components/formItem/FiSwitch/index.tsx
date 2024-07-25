import { FC } from 'react'
import { Form, Switch, FormItemProps, SwitchProps } from 'antd'

const { Item } = Form

export const FiSwitch: FC<{
	formItemProps?: FormItemProps
	switchProps?: SwitchProps
}> = ({ formItemProps, switchProps }) => {
	return (
		<Item
			initialValue={false}
			getValueProps={(value) => (value === 'yes' ? { checked: true } : {})}
			normalize={(value) => (value ? 'yes' : 'no')}
			{...formItemProps}
		>
			<Switch {...switchProps} />
		</Item>
	)
}
