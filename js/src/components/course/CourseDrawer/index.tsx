import { FC } from 'react'
import { Drawer, DrawerProps, Tabs, TabsProps, Form } from 'antd'
import { CourseDescription } from '@/components/course/form'

// import './style.scss'

export * from './useCourseDrawer'

export const CourseDrawer: FC<DrawerProps> = (drawerProps) => {
  const onChange = (key: string) => {
    console.log(key)
  }

  const items: TabsProps['items'] = [
    {
      key: '1',
      forceRender: true,
      label: '課程描述',
      children: <CourseDescription />,
    },
    {
      key: '2',
      forceRender: true,
      label: 'QA設定',
      children: 'Content of Tab Pane 2',
    },
    {
      key: '3',
      forceRender: true,
      label: '課程公告',
      children: 'Content of Tab Pane 3',
    },
    {
      key: '4',
      forceRender: true,
      label: '其他設定',
      children: 'Content of Tab Pane 3',
    },
    {
      key: '5',
      forceRender: true,
      label: '銷售方案',
      children: 'Content of Tab Pane 3',
    },
  ]

  return (
    <>
      <Drawer {...drawerProps}>
        <Form layout="vertical">
          <Tabs
            className="pc-course-drawer-tabs"
            defaultActiveKey={items?.[0]?.key}
            items={items}
            onChange={onChange}
            centered
          />
        </Form>
      </Drawer>
    </>
  )
}
