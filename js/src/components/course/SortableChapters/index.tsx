import { useState, useEffect, memo } from 'react'
import { SortableTree, TreeData } from '@ant-design/pro-editor'
import { TChapterRecord } from '@/pages/admin/Courses/CourseTable/types'
import { Form, message, Card } from 'antd'
import NodeRender from './NodeRender'
import { chapterToTreeNode, treeToParams } from './utils'
import {
	useCustomMutation,
	useApiUrl,
	useInvalidate,
	useList,
	HttpError,
} from '@refinedev/core'
import { isEqual as _isEqual } from 'lodash-es'
import { ChapterEdit } from '@/components/chapters'

const LoadingChapters = () => (
	<div className="pl-3">
		{new Array(10).fill(0).map((_, index) => (
			<div
				key={index}
				className=" bg-gray-100 h-7 rounded-sm mb-1 animate-pulse"
			/>
		))}
	</div>
)

const SortableChaptersComponent = () => {
	const form = Form.useFormInstance()
	const courseId = form?.getFieldValue('id')
	const { data: chaptersData, isLoading: isListLoading } = useList<
		TChapterRecord,
		HttpError
	>({
		resource: 'chapters',
		filters: [
			{
				field: 'post_parent',
				operator: 'eq',
				value: courseId,
			},
		],
		pagination: {
			current: 1,
			pageSize: -1,
		},
	})

	const chapters = chaptersData?.data || []

	const [treeData, setTreeData] = useState<TreeData<TChapterRecord>>([])
	const [originTree, setOriginTree] = useState<TreeData<TChapterRecord>>([])
	const invalidate = useInvalidate()

	const apiUrl = useApiUrl()
	const { mutate, isLoading } = useCustomMutation()

	useEffect(() => {
		if (!isListLoading) {
			const chapterTree = chapters?.map(chapterToTreeNode)
			setTreeData(chapterTree)
			setOriginTree(chapterTree)
		}
	}, [isListLoading])

	const handleSave = (data: TreeData<TChapterRecord>) => {
		// 這個儲存只存新增，不存章節的細部資料
		message.loading({
			content: '排序儲存中...',
			key: 'chapter-sorting',
		})
		const from_tree = treeToParams(originTree, courseId)
		const to_tree = treeToParams(data, courseId)

		mutate(
			{
				url: `${apiUrl}/chapters/sort`,
				method: 'post',
				values: {
					from_tree,
					to_tree,
				},
			},
			{
				onSuccess: () => {
					message.success({
						content: '排序儲存成功',
						key: 'chapter-sorting',
					})
				},
				onError: () => {
					message.loading({
						content: '排序儲存失敗',
						key: 'chapter-sorting',
					})
				},
				onSettled: () => {
					invalidate({
						resource: 'courses',
						invalidates: ['list'],
					})
				},
			},
		)
	}

	const [selectedChapter, setSelectedChapter] = useState<TChapterRecord | null>(
		null,
	)

	return (
		<>
			<div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
				{isListLoading && <LoadingChapters />}
				{!isListLoading && (
					<SortableTree
						hideAdd
						treeData={treeData}
						onTreeDataChange={(data: TreeData<TChapterRecord>) => {
							const treeDataWithoutCollapsed = data?.map((item) => ({
								...item,
								collapsed: false,
							}))
							const dataWithoutCollapse = treeData?.map((item) => ({
								...item,
								collapsed: false,
							}))
							const isEqual = _isEqual(
								treeDataWithoutCollapsed,
								dataWithoutCollapse,
							)
							setTreeData(data)
							if (!isEqual) {
								handleSave(data)
							}
						}}
						renderContent={(node) => {
							return (
								<NodeRender
									node={node}
									setSelectedChapter={setSelectedChapter}
								/>
							)
						}}
						indentationWidth={48}
						sortableRule={({ activeNode, projected }) => {
							const activeNodeHasChild = !!activeNode.children.length
							const sortable = projected?.depth <= (activeNodeHasChild ? 0 : 1)
							if (!sortable) message.error('超過最大深度，無法執行')
							return sortable
						}}
					/>
				)}

				{selectedChapter && <ChapterEdit record={selectedChapter} />}
			</div>
		</>
	)
}

export const SortableChapters = memo(SortableChaptersComponent)
