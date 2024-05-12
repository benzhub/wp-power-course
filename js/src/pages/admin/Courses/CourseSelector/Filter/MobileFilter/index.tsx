import React, { useState, FC } from 'react'
import Filter from '../'
import { Drawer, FormProps } from 'antd'
import { SlidersOutlined, CloseOutlined } from '@ant-design/icons'

const index: FC<{
  searchFormProps: FormProps
}> = ({ searchFormProps }) => {
  const [open, setOpen] = useState(false)

  const showDrawer = () => {
    setOpen(true)
  }

  const onClose = () => {
    setOpen(false)
  }

  return (
    <>
      <span className="text-sm mr-4 cursor-pointer" onClick={showDrawer}>
        <SlidersOutlined className="mr-2" /> 更多篩選條件
      </span>
      <Drawer
        title="商品篩選條件"
        onClose={onClose}
        open={open}
        zIndex={999999}
        width="90%"
        placement="left"
        extra={<CloseOutlined onClick={onClose} />}
        forceRender={true}
        closeIcon={null}
      >
        <Filter searchFormProps={searchFormProps} />
      </Drawer>
    </>
  )
}

export default index
