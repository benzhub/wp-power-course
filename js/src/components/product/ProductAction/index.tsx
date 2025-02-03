import React, { FC } from 'react'
import { TCourseBaseRecord } from '@/pages/admin/Courses/List/types'
import ToggleVisibility from './ToggleVisibility'
import { ExportOutlined } from '@ant-design/icons'
import { Tooltip, Button } from 'antd'
import { siteUrl, course_permalink_structure } from '@/utils'
import { SiGoogleclassroom } from 'react-icons/si'
import { DuplicateButton } from '@/components/general'

export const ProductAction: FC<{
	record: TCourseBaseRecord
}> = ({ record }) => {
	return (
		<div className="flex gap-1">
			<DuplicateButton
				id={record?.id}
				invalidateProps={{ resource: 'courses' }}
				tooltipProps={{ title: '複製課程' }}
			/>
			<Tooltip title="開啟課程教室">
				<Button
					type="text"
					icon={
						<SiGoogleclassroom className="relative top-0.5 text-gray-400" />
					}
					href={`${siteUrl}/classroom/${record?.slug}`}
					target="_blank"
					rel="noreferrer"
				/>
			</Tooltip>
			<Tooltip title="開啟課程銷售頁">
				<Button
					type="text"
					href={`${siteUrl}/${course_permalink_structure}/${record?.slug}`}
					target="_blank"
					rel="noreferrer"
					icon={<ExportOutlined className="text-gray-400" />}
				/>
			</Tooltip>
			<ToggleVisibility record={record} />
		</div>
	)
}
