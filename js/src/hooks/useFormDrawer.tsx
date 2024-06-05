import { useState, useEffect } from 'react'
import { DrawerProps, Button, FormInstance } from 'antd'
import { useCreate, useUpdate, useInvalidate } from '@refinedev/core'

export function useFormDrawer<DataType>({
  form,
  record,
  resource = 'courses',
}: {
  form: FormInstance
  record?: DataType & { id: string; depth: number }
  resource?: string
}) {
  const [open, setOpen] = useState(false)
  const isUpdate = !!record // 如果沒有傳入 record 就走新增課程，否則走更新課程
  const invalidate = useInvalidate()

  const show = () => {
    setOpen(true)
  }

  const close = () => {
    setOpen(false)
  }

  const { mutate: create, isLoading: isLoadingCreate } = useCreate()
  const { mutate: update, isLoading: isLoadingUpdate } = useUpdate()

  const invalidateCourse = () => {
    if (resource === 'chapters') {
      invalidate({
        resource: 'courses',
        invalidates: ['list'],
      })
    }
  }

  const handleSave = () => {
    form.validateFields().then(() => {
      const values = form.getFieldsValue()

      if (isUpdate) {
        update(
          {
            id: record?.id,
            resource,
            values,
          },
          {
            onSuccess: () => {
              invalidateCourse()
            },
          },
        )
      } else {
        create(
          {
            resource,
            values,
          },
          {
            onSuccess: () => {
              close()
              form.resetFields()
              invalidateCourse()
            },
          },
        )
      }
    })
  }

  const itemLabel = getItemLabel(resource, record?.depth)

  const drawerProps: DrawerProps = {
    title: `${isUpdate ? '編輯' : '新增'}${itemLabel}`,
    forceRender: true,
    onClose: close,
    open,
    width: '50%',
    extra: (
      <Button
        type="primary"
        onClick={handleSave}
        loading={isUpdate ? isLoadingUpdate : isLoadingCreate}
      >
        儲存
      </Button>
    ),
  }

  useEffect(() => {
    if (record?.id) {
      form.setFieldsValue(record)
    }
  }, [record?.id])

  return {
    open,
    setOpen,
    show,
    close,
    drawerProps,
  }
}

function getItemLabel(resource: string, depth: number | undefined) {
  if (resource === 'courses') return '課程'
  return depth === 0 ? '章節' : '段落'
}
