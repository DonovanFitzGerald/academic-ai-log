import type { UseLog, UseCase } from '@/types/assistant';
import { MoveRight } from 'lucide-react';

const UseLogDisplay = ({ useLog }: { useLog: UseLog }) => {
    if (!useLog) return;
    console.log(useLog);
    return (
        <div className="mx-auto flex max-w-2xl flex-1 flex-col gap-6 p-6">
            <div className="">
                <p className="mt-2 font-medium">
                    Use Cases: {useLog.total_use_cases}
                </p>
                <p className="mt-2 text-gray-400">{useLog.summary_statement}</p>
            </div>

            <div className="flex flex-col gap-8">
                {useLog.use_cases.map((useCase, index: number) => (
                    <UseCaseCard key={index} useCase={useCase} index={index} />
                ))}
            </div>
        </div>
    );
};

const UseCaseCard = ({
    useCase,
    index,
}: {
    useCase: UseCase;
    index: number;
}) => {
    return (
        <div className="rounded-lg border border-gray-100 bg-white p-4 shadow-sm">
            <div className="flex justify-between">
                <h3 className="mb-2 text-lg font-semibold">{useCase.label}</h3>
                <h3 className="text-neutral-400">{index}</h3>
            </div>
            <div className="flex flex-col gap-2 text-sm text-neutral-600">
                <p>{useCase.evidence}</p>
                <p className="text-neutral-400">
                    <span className="font-medium">Assistant Role:</span>{' '}
                    {useCase.assistant_role}
                </p>
                <div className="flex justify-between pt-2 text-xs text-neutral-400 uppercase">
                    <span className="">{useCase.input_type.join(', ')}</span>
                    <MoveRight className="h-4" />
                    <span className="">{useCase.output_type.join(', ')}</span>
                </div>
            </div>
        </div>
    );
};

export { UseLogDisplay, UseCaseCard };
