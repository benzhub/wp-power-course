import { useState, useEffect, useRef } from 'react'
import { DrawerProps, Button, FormInstance, Popconfirm } from 'antd'
import { useCreate, useUpdate, useInvalidate } from '@refinedev/core'
import { TUserRecord } from '@/pages/admin/Courses/CourseSelector/types'
import { toFormData } from 'axios'
import { isEqual } from 'lodash-es'

export function useUserFormDrawer({
  form,
  resource = 'users',
  drawerProps,
}: {
  form: FormInstance
  resource?: string
  drawerProps?: DrawerProps
}) {
  // const [record, setRecord] = useAtom(selectedRecordAtom)
  const [open, setOpen] = useState(false)
  const [record, setRecord] = useState<TUserRecord | undefined>(undefined)
  const isUpdate = !!record // 如果沒有傳入 record 就走新增課程，否則走更新課程
  const closeRef = useRef<HTMLDivElement>(null)

  const invalidate = useInvalidate()

  const show = (theRecord?: TUserRecord) => () => {
    setRecord(theRecord)
    setOpen(true)
  }

  const close = () => {
    // 與原本的值相比是否有變更
    const newValues = form.getFieldsValue()
    const fieldNames = Object.keys(newValues).filter(
      (fieldName) => !['files'].includes(fieldName),
    )
    const isEquals = fieldNames.every((fieldName) => {
      const originValue = record?.[fieldName as keyof typeof record]
      const newValue = newValues[fieldName]

      return isEqual(originValue, newValue)
    })

    if (!isEquals) {
      closeRef?.current?.click()
    } else {
      setOpen(false)
    }
  }

  const { mutate: create, isLoading: isLoadingCreate } = useCreate()
  const { mutate: update, isLoading: isLoadingUpdate } = useUpdate()

  const invalidateUser = () => {
    invalidate({
      resource,
      invalidates: ['list'],
    })
  }

  const handleSave = () => {
    form.validateFields().then(() => {
      const values = form.getFieldsValue()

      const formData = toFormData(values)

      if (isUpdate) {
        update(
          {
            id: record?.id,
            resource,
            values: formData,
            meta: {
              headers: { 'Content-Type': 'multipart/form-data;' },
            },
          },
          {
            onSuccess: () => {
              invalidateUser()
            },
          },
        )
      } else {
        create(
          {
            resource,
            values: formData,
            meta: {
              headers: { 'Content-Type': 'multipart/form-data;' },
            },
          },
          {
            onSuccess: () => {
              setOpen(false)
              form.resetFields()
              invalidateUser()
            },
          },
        )
      }
    })
  }

  const mergedDrawerProps: DrawerProps = {
    title: `${isUpdate ? '編輯' : '新增'}講師`,
    forceRender: true,
    push: false,
    onClose: close,
    open,
    width: '50%',
    extra: (
      <div className="flex">
        <Popconfirm
          title="你儲存了嗎?"
          description="確認關閉後，你的編輯可能會遺失，請確認操作"
          placement="leftTop"
          okText="確認關閉"
          cancelText="取消"
          onConfirm={() => {
            setOpen(false)
          }}
        >
          <p ref={closeRef} className="">
            &nbsp;
          </p>
        </Popconfirm>
        <Button
          type="primary"
          onClick={handleSave}
          loading={isUpdate ? isLoadingUpdate : isLoadingCreate}
        >
          儲存
        </Button>
      </div>
    ),
    ...drawerProps,
  }

  useEffect(() => {
    if (record?.id) {
      form.setFieldsValue(record)
    } else {
      form.resetFields()
    }
  }, [record])

  return {
    open,
    setOpen,
    show,
    close,
    drawerProps: mergedDrawerProps,
  }
}
