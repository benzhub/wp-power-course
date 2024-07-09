import { useState } from 'react'
import { useSelect } from '@refinedev/antd'
import { Select, Space, Button, Form, message } from 'antd'
import { TUserRecord } from '@/pages/admin/Courses/CourseSelector/types'
import { useCustomMutation, useApiUrl, useInvalidate } from '@refinedev/core'

const index = () => {
  const apiUrl = useApiUrl()
  const invalidate = useInvalidate()
  const [userIds, setUserIds] = useState<string[]>([])
  const form = Form.useFormInstance()
  const watchId = Form.useWatch(['id'], form)

  const { selectProps } = useSelect<TUserRecord>({
    resource: 'users',
    optionLabel: 'display_name',
    optionValue: 'id',
    filters: [
      {
        field: 'search',
        operator: 'eq',
        value: '',
      },
      {
        field: 'number',
        operator: 'eq',
        value: '20',
      },
    ],
    onSearch: (value) => [
      {
        field: 'search',
        operator: 'eq',
        value,
      },
    ],
    queryOptions: {
      enabled: !!watchId,
    },
  })

  // add student mutation
  const { mutate: addStudent, isLoading } = useCustomMutation()

  const handleAdd = () => {
    addStudent(
      {
        url: `${apiUrl}/add-students/${watchId}`,
        method: 'post',
        values: {
          user_ids: userIds,
        },
        config: {
          headers: {
            'Content-Type': 'multipart/form-data;',
          },
        },
      },
      {
        onSuccess: () => {
          message.success({
            content: '新增學員成功！',
            key: 'add-students',
          })
          invalidate({
            resource: 'students',
            invalidates: ['list'],
          })
        },
        onError: () => {
          message.error({
            content: '新增學員失敗！',
            key: 'add-students',
          })
        },
      },
    )
  }

  return (
    <Space.Compact className="w-full">
      <Button type="primary" onClick={handleAdd} loading={isLoading}>
        新增學員
      </Button>
      <Select
        {...selectProps}
        className="w-full"
        placeholder="試試看搜尋 Email, 名稱, ID"
        mode="multiple"
        allowClear
        onChange={(value) => {
          setUserIds(value as unknown as string[])
        }}
      />
    </Space.Compact>
  )
}

export default index
